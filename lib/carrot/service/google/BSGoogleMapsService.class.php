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

	private function queryGeocode ($address) {
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

	private function getTable () {
		if (!$this->table) {
			$this->table = new BSGeocodeEntryHandler;
		}
		return $this->table;
	}

	/**
	 * script要素を返す
	 *
	 * @access public
	 * @param BSGeocodeEntry $geocode ジオコード
	 * @param BSParameterHolder $params パラメータ配列
	 * @return BSDivisionElement
	 * @static
	 */
	static public function getScriptElement (BSGeocodeEntry $geocode, BSParameterHolder $params) {
		$element = new BSDivisionElement;
		$inner = $element->addElement(new BSDivisionElement);
		$script = $element->addElement(new BSScriptElement);

		$inner->setID($params['id']);
		$inner->setStyle('width', $params['width']);
		$inner->setStyle('height', $params['height']);
		$inner->setBody('Loading...');

		$statement = new BSStringFormat('handleGoogleMaps($(%s), %f, %f);');
		$statement[] = BSJavaScriptUtility::quote($params['id']);
		$statement[] = $geocode['lat'];
		$statement[] = $geocode['lng'];
		$script->setBody($statement->getContents());

		return $element;
	}

	/**
	 * サイトを直接開くURLを返す
	 *
	 * @access public
	 * @param string $addr 住所
	 * @param string BSUserAgent $useragent 対象ブラウザ
	 * @return BSHTTPURL
	 * @static
	 */
	static public function getURL ($addr, BSUserAgent $useragent = null) {
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
		$url->setParameter('q', $addr);
		return $url;
	}
}

/* vim:set tabstop=4: */
