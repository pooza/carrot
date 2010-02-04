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
}

/* vim:set tabstop=4: */
