<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage database.table
 */

/**
 * テーブルイテレータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSTableIterator extends BSIterator {
	private $table;

	/**
	 * @access public
	 * @param BSTableHandler $table テーブル
	 */
	public function __construct (BSTableHandler $table) {
		$this->table = $table;
		foreach ($table->getResult() as $row) {
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

	/**
	 * カーソルを終端に進める
	 *
	 * @access public
	 * @return mixed 最後のエントリー
	 */
	public function forward () {
		$this->cursor = $this->table->count() - 1;
		return $this->current();
	}
}

