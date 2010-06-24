<?php
/**
 * @package org.carrot-framework
 * @subpackage geocode
 */

/**
 * ジオコード エントリーテーブル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSGeocodeEntryHandler extends BSTableHandler {
	const PATTERN = '^((lat|lng)=[[:digit:]]+\\.[[:digit:]]+([ ,]+)?)+$';

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
		return new BSArray(array(
			'id' => 'integer NOT NULL PRIMARY KEY',
			'addr' => 'varchar(128)',
			'lat' => 'float',
			'lng' => 'float',
		));
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
	static function parse ($value) {
		if (mb_ereg(self::PATTERN, $value)) {
			$info = new BSArray;
			foreach(mb_split('[ ,]+', $address) as $entry) {
				$entry = BSString::explode('=', $entry);
				$info[$entry[0]] = $entry[1];
			}
			return $info;
		}
	}
}

/* vim:set tabstop=4: */
