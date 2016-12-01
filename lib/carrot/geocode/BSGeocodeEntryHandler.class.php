<?php
/**
 * @package org.carrot-framework
 * @subpackage geocode
 */

/**
 * ジオコード エントリーテーブル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSGeocodeEntryHandler extends BSTableHandler {
	const PATTERN = '^((lat|lng|lon)=[[:digit:]]+\\.[[:digit:]]+([ ,]+)?)+$';

	/**
	 * レコード追加可能か？
	 *
	 * @access protected
	 * @return boolean レコード追加可能ならTrue
	 */
	protected function isInsertable () {
		return true;
	}

	/**
	 * テーブル名を返す
	 *
	 * @access public
	 * @return string テーブル名
	 */
	public function getName () {
		return 'geocode_entry';
	}

	/**
	 * データベースを返す
	 *
	 * @access public
	 * @return BSDatabase データベース
	 */
	public function getDatabase () {
		return BSDatabase::getInstance('geocode');
	}

	/**
	 * 登録
	 *
	 * @access public
	 * @param string $addr 住所
	 * @param BSArray $coord ジオコード座標
	 * @return BSGeocodeEntry 登録されたレコード
	 */
	public function register ($addr, BSArray $coord) {
		$values = clone $coord;
		$values['addr'] = $addr;
		if (BSString::isBlank($values['lng'])) {
			$values['lng'] = $values['lon'];
			$values->removeParameter($values['lon']);
		}

		if ($id = $this->createRecord($values)) {
			return $this->getRecord($id);
		}
	}

	/**
	 * スキーマを返す
	 *
	 * @access public
	 * @return BSArray フィールド情報の配列
	 */
	public function getSchema () {
		return new BSArray([
			'id' => 'integer NOT NULL PRIMARY KEY',
			'addr' => 'varchar(128)',
			'lat' => 'float',
			'lng' => 'float',
		]);
	}

	/**
	 * Geocode文字列をパースして、配列を返す
	 *
	 * "lat=0.00,lng=0.00" の様な文字列。
	 *
	 * @access public
	 * @return BSArray Geocode情報
	 * @static
	 */
	static public function parse ($value) {
		if (mb_ereg(self::PATTERN, $value)) {
			$info = new BSArray;
			foreach(mb_split('[ ,]+', $value) as $entry) {
				$entry = BSString::explode('=', $entry);
				switch ($key = $entry[0]) {
					case 'lon':
					case 'lng':
						$key = 'lng';
						break;
				}
				$info[$key] = $entry[1];
			}
			return $info;
		}
	}

	/**
	 * 小数点以下60進数の値を10進数に変換
	 *
	 * @access public
	 * @param string $dms 小数点以下が60進数である座標値
	 * @return float 10進数に変換された座標値
	 * @link http://d.hatena.ne.jp/Xephy/20100209/1265743457 参考
	 * @static
	 */
	static public function dms2deg ($dms) {
		$dms = array_pad(explode('.', $dms), 4, '0');
		$deg = $dms[0] + $dms[1] / 60 + ($dms[2].'.'.$dms[3]) / 3600;
		return round($deg, 9);
	}
}

/* vim:set tabstop=4: */
