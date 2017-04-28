<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage service.google
 */

/**
 * Google Mapsクライアント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
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
			$port = BSNetworkService::getPort('https');
		}
		parent::__construct($host, $port);
		$this->useragent = $this->request->getUserAgent();
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
	public function createElement ($address, BSParameterHolder $params = null) {
		$params = BSArray::create($params);
		$params['address'] = $address;
		if (!$params['zoom']) {
			$params['zoom'] = BS_SERVICE_GOOGLE_MAPS_ZOOM;
		}

		if (!$geocode = $this->getGeocode($address)) {
			$message = new BSStringFormat('"%s" のジオコードが取得できません。');
			$message[] = $address;
			throw new BSServiceException($message);
		}

		if ($this->useragent->isMobile()) {
			$params->removeParameter('width');
			$params->removeParameter('height');
			return $this->createImageElement($geocode, $params);
		} else {
			$info = $this->useragent->getDisplayInfo();
			if (!$params['max_width'] && $info['width']) {
				$params['max_width'] = $info['width'];
			}
			if ($params['max_width'] && ($params['max_width'] < $params['width'])) {
				$params['width'] = $params['max_width'];
				$params['height'] = BSNumeric::round(
					$params['height'] * $params['width'] / $params['max_width']
				);
			}
			return $geocode->createElement($params);
		}
	}

	/**
	 * ジオコードを返す
	 *
	 * @access public
	 * @param string $address 住所等
	 * @return BSGeocodeEntry ジオコード
	 */
	public function getGeocode ($address) {
		return (new BSGoogleMapsGeocodingService)->getGeocode($address);
	}

	/**
	 * パスからリクエストURLを生成して返す
	 *
	 * @access public
	 * @param string $href パス
	 * @return BSHTTPURL リクエストURL
	 */
	public function createRequestURL ($href) {
		$url = parent::createRequestURL($href);
		$url->setParameter('key', BS_SERVICE_GOOGLE_MAPS_API_KEY);
		return $url;
	}

	/**
	 * img要素を返す
	 *
	 * @access protected
	 * @param BSGeocodeEntry $geocode ジオコード
	 * @param BSArray $params パラメータ配列
	 * @return BSDivisionElement
	 */
	protected function createImageElement (BSGeocodeEntry $geocode, BSArray $params) {
		$address = $params['address'];
		$params->removeParameter('address');
		$file = $this->getImageFile($geocode, $params);
		$info = $file->getImageInfo('roadmap', null, BSImageManager::FORCE_GIF);

		$image = new BSImageElement;
		$image->setURL(BSURL::create($info['url']));
		$container = new BSDivisionElement;
		if (BSString::isBlank($label = $params['label'])) {
			$anchor = $container->addElement(new BSAnchorElement);
			$anchor->link($image, $this->createPageURL($address, $params));
		} else {
			$container->addElement($image);
			$labelContainer = $container->addElement(new BSDivisionElement);
			$labelContainer->setAttribute('align', 'center');
			$anchor = $labelContainer->addElement(new BSAnchorElement);
			$anchor->setBody($label);
			$anchor->setURL($this->createPageURL($address, $params));
		}
		return $container;
	}

	private function createPageURL ($address, BSArray $params) {
		$url = BSURL::create();
		$url['host'] = self::DEFAULT_HOST;
		if ($geocode = $this->getGeocode($address)) {
			$url->setParameter('ll', $geocode->format());
		}
		if ($params['zoom']) {
			$url->setParameter('z', $params['zoom']);
		}
		return $url;
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
		$name = BSCrypt::digest([
			$geocode->format(),
			$params->join('|'),
		]);
		if (!$file = $dir->getEntry($name, 'BSImageFile')) {
			$response = $this->sendGET($this->getImageURL($geocode, $params)->getFullPath());
			$image = new BSImage;
			$image->setImage($response->getRenderer()->getContents());
			$file = $dir->createEntry($name, 'BSImageFile');
			$file->setRenderer($image);
			$file->save();
		}
		return $file;
	}

	/**
	 * Google Static Maps APIのクエリーURLを返す
	 *
	 * @access protected
	 * @param BSGeocodeEntry $geocode ジオコード
	 * @param BSArray $params パラメータ配列
	 * @return BSHTTPURL クエリーURL
	 * @link http://code.google.com/intl/ja/apis/maps/documentation/staticmaps/
	 */
	protected function getImageURL (BSGeocodeEntry $geocode, BSArray $params) {
		$info = $this->useragent->getDisplayInfo();
		$size = new BSStringFormat('%dx%d');
		$size[] = $info['width'];
		$size[] = BSNumeric::round($info['width'] * 0.75);

		$url = $this->createRequestURL('/maps/api/staticmap');
		$url->setParameter('format', BS_SERVICE_GOOGLE_STATIC_MAPS_FORMAT);
		$url->setParameter('maptype', 'mobile');
		$url->setParameter('center', $geocode->format());
		$url->setParameter('markers', $geocode->format());
		$url->setParameter('size', $size->getContents());
		$url->setParameter('key', BS_SERVICE_GOOGLE_STATIC_MAPS_API_KEY);
		foreach ($params as $key => $value) {
			$url->setParameter($key, $value);
		}
		return $url;
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('Google Maps "%s"', $this->getName());
	}
}

/* vim:set tabstop=4: */
