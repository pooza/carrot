<?php
/**
 * @package org.carrot-framework
 */

/**
 * 基底イテレータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSIterator implements Iterator {
	protected $keys = array();
	protected $values = array();
	protected $cursor;

	/**
	 * @access public
	 * @param $array 対象配列
	 */
	public function __construct ($array) {
		if ($array instanceof BSArray) {
			$this->values = $array->getParameters();
		} else if (is_array($array)) {
			$this->values = $array;
		} else {
			throw new BSException('引数は配列ではありません。');
		}
		$this->keys = array_keys($this->values);
	}

	/**
	 * カーソルを巻き戻す
	 *
	 * @access public
	 * @return mixed 最初のエントリー
	 */
	public function rewind () {
		$this->cursor = 0;
		return $this->current();
	}

	/**
	 * 最初のエントリーを返す
	 *
	 * forwardのエイリアス
	 *
	 * @access public
	 * @return mixed 最初のエントリー
	 * @final
	 */
	final public function getFirst () {
		return $this->rewind();
	}

	/**
	 * カーソルを終端に進める
	 *
	 * @access public
	 * @return mixed 最後のエントリー
	 */
	public function forward () {
		$this->cursor = count($this->values) - 1;
		return $this->current();
	}

	/**
	 * 最後のエントリーを返す
	 *
	 * rewindのエイリアス
	 *
	 * @access public
	 * @return mixed 最後のエントリー
	 * @final
	 */
	final public function getLast () {
		return $this->forward();
	}

	/**
	 * 現在のエントリーを返す
	 *
	 * @access public
	 * @return mixed エントリー
	 */
	public function current () {
		if ($this->valid()) {
			return $this->values[$this->key()];
		}
	}

	/**
	 * 次のエントリーを返す
	 *
	 * @access public
	 * @return mixed エントリー
	 */
	public function next () {
		$this->cursor ++;
		return $this->current();
	}

	/**
	 * 前のエントリーを返す
	 *
	 * @access public
	 * @return mixed エントリー
	 */
	public function prev () {
		$this->cursor --;
		return $this->current();
	}

	/**
	 * 現在のカーソル位置を返す
	 *
	 * @access public
	 * @return integer カーソル位置
	 */
	public function key () {
		if ($this->valid()) {
			return $this->keys[$this->cursor];
		}
	}

	/**
	 * 現在のカーソル位置に正しいエントリーが存在するか？
	 *
	 * @access public
	 * @return boolean 正しいエントリーが存在するならTrue
	 */
	public function valid () {
		return isset($this->keys[$this->cursor]);
	}
}

/* vim:set tabstop=4 ai: */
?>