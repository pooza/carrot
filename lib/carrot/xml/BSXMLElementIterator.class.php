<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage xml
 */

/**
 * XML要素イテレータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSXMLElementIterator.class.php 5 2007-07-25 08:04:01Z pooza $
 */
class BSXMLElementIterator implements Iterator {
	private $root;
	private $elements = array();
	private $cursor = 0;

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param BSXMLElement $element XML要素
	 */
	public function __construct (BSXMLElement $element) {
		$this->root = $element;
		$this->elements = $this->root->getElements();
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
	 * 現在のエントリーを返す
	 *
	 * @access public
	 * @return mixed ファイル又はディレクトリ
	 */
	public function current () {
		return $this->elements[$this->cursor];
	}

	/**
	 * 次のエントリーを返す
	 *
	 * @access public
	 * @return mixed ファイル又はディレクトリ
	 */
	public function next () {
		$this->cursor ++;
		if ($this->valid()) {
			return $this->elements[$this->cursor];
		}
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
	 * 現在のカーソル位置に正しいエントリーが存在するか
	 *
	 * @access public
	 * @return boolean 正しいエントリーが存在するならTrue
	 */
	public function valid () {
		return isset($this->elements[$this->cursor]);
	}
}

/* vim:set tabstop=4 ai: */
?>