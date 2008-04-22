<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage database
 */

/**
 * テーブルイテレータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSTableIterator.class.php 231 2008-04-22 04:37:26Z pooza $
 */
class BSTableIterator implements Iterator {
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
}

/* vim:set tabstop=4 ai: */
?>