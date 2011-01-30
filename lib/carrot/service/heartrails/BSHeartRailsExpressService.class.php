<?php
/**
 * @package org.carrot-framework
 * @subpackage service.heartrails
 */

/**
 * HeartRails Expressクライアント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSHeartRailsExpressService extends BSCurlHTTP {
	const DEFAULT_HOST = 'express.heartrails.com';
	const FORCE_QUERY = 1;

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
	 * 最寄り駅を返す
	 *
	 * @access public
	 * @param BSGeocodeEntry $geocode ジオコード
	 * @param integer $flags フラグのビット列
	 *   self::FORCE_QUERY 新規取得を強制
	 * @return BSArray 最寄り駅の配列
	 */
	public function getStations (BSGeocodeEntry $geocode, $flags = null) {
		$controller = BSController::getInstance();
		$name = new BSStringFormat('%s.%s.%011.7f-%011.7f');
		$name[] = get_class($this);
		$name[] = __FUNCTION__;
		$name[] = $geocode['lat'];
		$name[] = $geocode['lng'];
		$name = $name->getContents();
		$date = BSDate::getNow()->setAttribute('day', '-7');
		if (($flags & self::FORCE_QUERY) || !$controller->getAttribute($name, $date)) {
			try {
				$controller->setAttribute($name, $this->queryStations($geocode));
				$message = new BSStringFormat('%s,%sの最寄り駅を取得しました。');
				$message[] = $geocode['lat'];
				$message[] = $geocode['lng'];
				BSLogManager::getInstance()->put($message, $this);
			} catch (Exception $e) {
			}
		}
		return $controller->getAttribute($name);
	}

	private function queryStations (BSGeocodeEntry $geocode) {
		$params = new BSWWWFormRenderer;
		$params['method'] = 'getStations';
		$params['x'] = $geocode['lng'];
		$params['y'] = $geocode['lat'];
		$path = '/api/json?' . $params->getContents();
		$response = $this->sendGET($path);
		$serializer = new BSJSONSerializer;
		$result = $serializer->decode($response->getRenderer()->getContents());

		$stations = new BSArray;
		$x = null;
		$y = null;
		foreach ($result['response']['station'] as $entry) {
			if (($x !== $entry['x']) && ($y !== $entry['y'])) {
				$station = new BSArray($entry);
				$station['line'] = new BSArray($entry['line']);
				$stations[] = $station;
				$x = $entry['x'];
				$y = $entry['y'];
			} else {
				$station['line'][] = $entry['line'];
			}
		}
		return $stations;
	}
}

/* vim:set tabstop=4: */
