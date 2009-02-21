<?php
/**
 * @package org.carrot-framework
 * @subpackage view
 */

/**
 * プレーンテキストレンダラー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSPlainTextRenderer implements BSTextRenderer, IteratorAggregate {
	private $encoding = 'UTF-8';
	private $lineSeparator = "\n";
	private $width = null;
	private $contents;

	/**
	 * 出力内容を返す
	 *
	 * @access public
	 */
	public function getContents () {
		$contents = $this->contents;

		if ($this->width) {
			$contents = BSString::split($contents, $this->width);
		}

		$contents = BSString::convertLineSeparator($contents, $this->lineSeparator);
		$contents = BSString::convertEncoding($contents, $this->getEncoding());
		return $contents;
	}

	/**
	 * 出力内容を設定
	 *
	 * @param string $contents 出力内容
	 * @access public
	 */
	public function setContents ($contents) {
		$this->contents = $contents;
	}

	/**
	 * 出力内容のサイズを返す
	 *
	 * @access public
	 * @return integer サイズ
	 */
	public function getSize () {
		return strlen($this->getContents());
	}

	/**
	 * メディアタイプを返す
	 *
	 * @access public
	 * @return string メディアタイプ
	 */
	public function getType () {
		return BSMIMEType::getType('txt');
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		return true;
	}

	/**
	 * エラーメッセージを返す
	 *
	 * @access public
	 * @return string エラーメッセージ
	 */
	public function getError () {
		return null;
	}

	/**
	 * エンコードを返す
	 *
	 * @access public
	 * @return string PHPのエンコード名
	 */
	public function getEncoding () {
		return $this->encoding;
	}

	/**
	 * エンコードを設定
	 *
	 * @access public
	 * @param string $encoding エンコード名
	 */
	public function setEncoding ($encoding) {
		$this->encoding = $encoding;
	}

	/**
	 * 改行コードを設定
	 *
	 * @access public
	 * @param string $separator 改行コード
	 */
	public function setLineSeparator ($separator) {
		$this->lineSeparator = $separator;
	}

	/**
	 * 行幅を設定
	 *
	 * @access public
	 * @param integer $width 行幅
	 */
	public function setWidth ($width) {
		$this->width = $width;
	}

	/**
	 * イテレータを返す
	 *
	 * @access public
	 * @return BSIterator イテレータ
	 */
	public function getIterator () {
		return new BSIterator(BSString::explode($this->lineSeparator, $this->contents));
	}
}

/* vim:set tabstop=4: */
