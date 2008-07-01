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
 * @version $Id$
 */
class BSXMLElementIterator implements BSIterator {
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

	/**
	 * 最初の要素を返す
	 *
	 * @access public
	 * @return mixed 最初の要素
	 */
	public function getFirst () {
		return $this->elements[0];
	}

	/**
	 * 最後の要素を返す
	 *
	 * @access public
	 * @return mixed 最後の要素
	 */
	public function getLast () {
		return $this->elements[count($this->elements) - 1];
	}
}

/* vim:set tabstop=4 ai: */
?>