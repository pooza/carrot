<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage database.table
 */

/**
 * テーブルイテレータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSTableIterator extends BSIterator {
	private $table;

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param BSTableHandler $table テーブル
	 */
	public function __construct (BSTableHandler $table) {
		$this->table = $table;
		foreach ($table->getContents() as $row) {
			$this->keys[] = $row[$table->getKeyField()];
		}
	}

	/**
	 * 現在のレコードを返す
	 *
	 * @access public
	 * @return BSRecord レコード
	 */
	public function current () {
		return $this->table->getRecord(parent::key());
	}
}

/* vim:set tabstop=4 ai: */
?>