<?php
/**
 * @package org.carrot-framework
 * @subpackage service.google
 */

/**
 * Google Mapsクライアント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSGoogleMapsService extends BSCurlHTTP {
	private $table;
	private $useragent;
	const DEFAULT_HOST = 'maps.google.com';

	/**
	 * @access public
	 * @param BSHost $host ホスト
	 * @param integer $port ポート
	 */
	public function __construct (BSHost $host = null, $port = null) {
		if (!$host) {
			$host = new BSHost(self::DEFAULT_HOST);
		}
		parent::__construct($host, $port);
		$this->useragent = BSRequest::getInstance()->getUserAgent();
	}

	/**
	 * 対象UserAgentを設定
	 *
	 * @access public
	 * @param BSUserAgent $useragent 対象UserAgent
	 */
	public function setUserAgent (BSUserAgent $useragent) {
		$this->useragent = $useragent;
	}

	/**
	 * 要素を返す
	 *
	 * @access public
	 * @param string $address 住所等
	 * @param BSParameterHolder $params パラメータ配列
	 * @return BSDivisionElement
	 */
	public function getElement ($address, BSParameterHolder $params = null) {
		$params = new BSArray($params);
		$params['address'] = $address;
		if (!$params['zoom']) {
			$params['zoom'] = BS_SERVICE_GOOGLE_MAPS_ZOOM;
		}

		if (!$geocode = $this->getGeocode($address)) {
			$message = new BSStringFormat('"%s" のジオコードが取得できません。');
			$message[] = $address;
			throw new BSGeocodeException($message);
		}

		if ($this->useragent->isMobile()) {
			$params->removeParameter('width');
			$params->removeParameter('height');
			return $this->getImageElement($geocode, $params);
		} else {
			return $this->getScriptElement($geocode, $params);
		}
	}

	/**
	 * script要素を返す
	 *
	 * @access protected
	 * @param BSGeocodeEntry $geocode ジオコード
	 * @param BSArray $params パラメータ配列
	 * @return BSDivisionElement
	 */
	protected function getScriptElement (BSGeocodeEntry $geocode, BSArray $params) {
		$container = new BSDivisionElement;
		$inner = $container->addElement(new BSDivisionElement);
		$script = $container->addElement(new BSScriptElement);

		$inner->setID('map_' . BSCrypt::getDigest($params['address']));
		$inner->setStyle('width', $params['width']);
		$inner->setStyle('height', $params['height']);
		$inner->setBody('Loading...');

		$statement = new BSStringFormat('handleGoogleMaps($(%s), %f, %f, %d);');
		$statement[] = BSJavaScriptUtility::quote($inner->getID());
		$statement[] = $geocode['lat'];
		$statement[] = $geocode['lng'];
		$statement[] = $params['zoom'];
		$script->setBody($statement->getContents());
		return $container;
	}

	/**
	 * ジオコードを返す
	 *
	 * @access public
	 * @param string $address 住所等
	 * @return BSGeocodeEntry ジオコード
	 */
	public function getGeocode ($address) {
		$values = array('addr' => $address);
		if (!$entry = $this->getTable()->getRecord($values)) {
			if ($result = $this->queryGeocode($address)) {
				$entry = $this->getTable()->register($address, $result);
			}
		}
		return $entry;
	}

	protected function queryGeocode ($address) {
		$params = new BSWWWFormRenderer;
		$params['q'] = $address;
		$params['output'] = 'json';
		$params['key'] = BS_SERVICE_GOOGLE_MAPS_API_KEY;
		$path = '/maps/geo?' . $params->getContents();
		$response = $this->sendGetRequest($path);

		$serializer = new BSJSONSerializer;
		$result = $serializer->decode($response->getBody());
		if (isset($result['Placemark'][0]['Point']['coordinates'])) {
			$coord = $result['Placemark'][0]['Point']['coordinates'];
			return new BSArray(array(
				'lat' => $coord[1],
				'lng' => $coord[0],
			));
		}
	}

	protected function getTable () {
		if (!$this->table) {
			$this->table = new BSGeocodeEntryHandler;
		}
		return $this->table;
	}

	/**
	 * img要素を返す
	 *
	 * @access protected
	 * @param BSGeocodeEntry $geocode ジオコード
	 * @param BSArray $params パラメータ配列
	 * @return BSDivisionElement
	 */
	protected function getImageElement (BSGeocodeEntry $geocode, BSArray $params) {
		$address = $params['address'];
		$params->removeParameter('address');

		$container = new BSDivisionElement;
		$anchor = $container->addElement(new BSAnchorElement);
		$anchor->setURL(self::getURL($address, $this->useragent));
		$caption = $container->addElement(new BSDivisionElement);
		$caption->setBody($address);

		$image = $anchor->addElement(new BSImageElement);
		$file = $this->getImageFile($geocode, $params);
		$url = BSFileUtility::getURL('maps');
		$url['path'] .= $file->getName();
		$image->setURL($url);

		return $container;
	}

	/**
	 * 地図画像ファイルを返す
	 *
	 * @access protected
	 * @param BSGeocodeEntry $geocode ジオコード
	 * @param BSArray $params パラメータ配列
	 * @return BSImageFile 画像ファイル
	 */
	protected function getImageFile (BSGeocodeEntry $geocode, BSArray $params) {
		$dir = BSFileUtility::getDirectory('maps');
		$name = BSCrypt::getDigest(array($geocode->format(), $params->join('|')));
		if (!$file = $dir->getEntry($name, 'BSImageFile')) {
			$image = new BSImage;
			$image->setImage($this->getImageURL($geocode, $params)->fetch());
			$file = $dir->createEntry($name, 'BSImageFile');
			$file->setRenderer($image);
			$file->save();
		}
		return $file;
	}

	/**
	 * Google Static Maps APIのURLを返す
	 *
	 * @access protected
	 * @param BSGeocodeEntry $geocode ジオコード
	 * @param BSArray $params パラメータ配列
	 * @return BSURL URL
	 * @see http://code.google.com/intl/ja/apis/maps/documentation/staticmaps/
	 */
	protected function getImageURL (BSGeocodeEntry $geocode, BSArray $params) {
		$info = $this->useragent->getDisplayInfo();
		$size = new BSStringFormat('%dx%d');
		$size[] = $info['width'];
		$size[] = BSNumeric::round($info['width'] * 0.75);

		$url = BSURL::getInstance();
		$url['host'] = self::DEFAULT_HOST;
		$url['path'] = '/staticmap';
		$url->setParameter('key', BS_SERVICE_GOOGLE_MAPS_API_KEY);
		$url->setParameter('format', BS_SERVICE_GOOGLE_MAPS_FORMAT);
		$url->setParameter('maptype', 'mobile');
		$url->setParameter('center', $geocode->format());
		$url->setParameter('markers', $geocode->format());
		$url->setParameter('size', $size->getContents());
		foreach ($params as $key => $value) {
			$url->setParameter($key, $value);
		}
		return $url;
	}

	/**
	 * サイトを直接開くURLを返す
	 *
	 * @access public
	 * @param string $address 住所等
	 * @param string BSUserAgent $useragent 対象ブラウザ
	 * @return BSHTTPURL
	 * @static
	 */
	static public function getURL ($address, BSUserAgent $useragent = null) {
		if (!$useragent) {
			$useragent = BSRequest::getInstance()->getUserAgent();
		}

		$url = BSURL::getInstance();
		if ($useragent->isMobile()) {
			$url['host'] = 'www.google.co.jp';
			$url['path'] = '/m/local';
		} else {
			$url['host'] = self::DEFAULT_HOST;
		}
		$url->setParameter('q', $address);
		return $url;
	}
}

/* vim:set tabstop=4: */
