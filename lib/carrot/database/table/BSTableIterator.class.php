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
class BSTableIterator implements BSIterator {
	private $table;
	private $cursor = 0;

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param BSTableHandler $table テーブル
	 */
	public function __construct (BSTableHandler $table) {
		$this->table = $table;
	}

	/**
	 * カーソルを巻き戻す
	 *
	 * @access public
	 */
	public function rewind () {
		$this->cursor = 0;
	}

	/**
	 * 現在のレコードを返す
	 *
	 * @access public
	 * @return BSRecord レコード
	 */
	public function current () {
		$records = $this->table->getResult();
		$record = $records[$this->cursor];
		$key = $record[$this->table->getKeyField()];
		return $this->table->getRecord($key);
	}

	/**
	 * 次のレコードを返す
	 *
	 * @access public
	 * @return BSRecord レコード
	 */
	public function next () {
		$records = $this->table->getResult();
		$record = $records[$this->cursor ++];
		$key = $record[$this->table->getKeyField()];
		return $this->table->getRecord($key);
	}

	/**
	 * 現在のカーソル位置を返す
	 *
	 * @access public
	 * @return integer カーソル位置
	 */
	public function key () {
		return $this->cursor;
	}

	/**
	 * 現在のカーソル位置に正しいレコードが存在するか
	 *
	 * @access public
	 * @return boolean 正しいレコードが存在するならTrue
	 */
	public function valid () {
		$records = $this->table->getResult();
		return isset($records[$this->cursor]);
	}

	/**
	 * 最初の要素を返す
	 *
	 * @access public
	 * @return mixed 最初の要素
	 */
	public function getFirst () {
		$records = $this->table->getResult();
		return $records[0];
	}

	/**
	 * 最後の要素を返す
	 *
	 * @access public
	 * @return mixed 最後の要素
	 */
	public function getLast () {
		$records = $this->table->getResult();
		return $records[count($records) - 1];
	}
}

/* vim:set tabstop=4 ai: */
?>