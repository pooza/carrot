<?php
/**
 * @package org.carrot-framework
 * @subpackage geocode
 */

/**
 * ジオコード エントリーレコード
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSGeocodeEntry extends BSRecord {
	private $stations;

	/**
	 * 書式化して返す
	 *
	 * @access public
	 * @param string $separator 区切り文字
	 * @return string 書式化した文字列
	 */
	public function format ($separator = ',') {
		return $this['lat'] . $separator . $this['lng'];
	}

	/**
	 * 最寄り駅を返す
	 *
	 * @access public
	 * @param integer $flags フラグのビット列
	 *   BSHeartRailsExpressService::FORCE_QUERY 新規取得を強制
	 * @return BSArray 最寄り駅
	 */
	public function getStations ($flags = null) {
		if (!$this->stations) {
			$this->stations = new BSArray;
			try {
				$service = new BSHeartRailsExpressService;
				$this->stations->setParameters($service->getStations($this, $flags));
			} catch (Exception $e) {
			}
		}
		return $this->stations;
	}
}

/* vim:set tabstop=4: */
