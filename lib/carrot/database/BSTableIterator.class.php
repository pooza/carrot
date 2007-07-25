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
 * @version $Id$
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
		$key = $this->getKey($record);
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
		$key = $this->getKey($record);
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
	 * 値配列からキー値を返す
	 *
	 * @access private
	 * @param string[] $record 値配列
	 * @return string[] キー値
	 */
	private function getKey ($record) {
		$key = array();
		foreach ($this->table->getKeyFields() as $name) {
			if (!isset($record[$name])) {
				// フィールド名が 'table.id' 形式の場合
				$name = explode('.', $name);
				$name = $name[1];
			}
			$key[$name] = $record[$name];
		}
		return $key;
	}
}

/* vim:set tabstop=4 ai: */
?>