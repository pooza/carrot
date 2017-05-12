<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage service.google
 */

/**
 * Google Maps Geocodingクライアント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSGoogleMapsGeocodingService extends BSCurlHTTP {
	private $table;
	const DEFAULT_HOST = 'maps.googleapis.com';

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
	}

	/**
	 * ジオコードを返す
	 *
	 * @access public
	 * @param string $address 住所等
	 * @return BSGeocodeEntry ジオコード
	 */
	public function getGeocode ($address) {
		$key = BSCrypt::digest([get_class($this), $address]);
		if (!$geocode = $this->controller->getAttribute($key)) {
			$geocode = $this->query($address);
			$this->controller->setAttribute($key, $geocode);
		}
		return new BSGeocodeEntry($geocode);
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
		$url->setParameter('key', BS_SERVICE_GOOGLE_MAPS_GEOCODING_API_KEY);
		return $url;
	}

	protected function query ($address) {
		$url = $this->createRequestURL('/maps/api/geocode/json');
		$url->setParameter('address', $address);
		$response = $this->sendGET($url->getFullPath());

		$serializer = new BSJSONSerializer;
		$result = $serializer->decode(base64_decode($response->getBody()));
		if (isset($result['results'][0]['geometry']['location'])) {
			return BSArray::create($result['results'][0]['geometry']['location']);
		}
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('Google Maps Geocoding "%s"', $this->getName());
	}
}

