<?php
/**
 * @package jp.co.b-shock.carrot
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
		$name = new BSStringFormat('%s.%s.%011.7f-%011.7f');
		$name[] = get_class($this);
		$name[] = __FUNCTION__;
		$name[] = $geocode['lat'];
		$name[] = $geocode['lng'];
		$name = $name->getContents();
		$date = BSDate::getNow()->setParameter('day', '-7');
		if (($flags & self::FORCE_QUERY) || !$this->controller->getAttribute($name, $date)) {
			try {
				$this->controller->setAttribute($name, $this->queryStations($geocode));
				$message = new BSStringFormat('%s,%sの最寄り駅を取得しました。');
				$message[] = $geocode['lat'];
				$message[] = $geocode['lng'];
				BSLogManager::getInstance()->put($message, $this);
			} catch (Exception $e) {
			}
		}
		return $this->controller->getAttribute($name);
	}

	private function queryStations (BSGeocodeEntry $geocode) {
		$url = $this->createRequestURL('/api/json');
		$url->setParameter('method', 'getStations');
		$url->setParameter('x', $geocode['lng']);
		$url->setParameter('y', $geocode['lat']);
		$response = $this->sendGET($url->getFullPath());

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

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('HeartRails Express "%s"', $this->getName());
	}
}

