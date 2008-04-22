<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage xml
 */

/**
 * 整形式XML文書
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSXMLDocument.class.php 97 2007-11-17 12:24:53Z pooza $
 */
class BSXMLDocument extends BSXMLElement implements BSRenderer {
	private $error;

	/**
	 * コンパイル前処理
	 *
	 * @access protected
	 */
	protected function preCompile () {
		parent::preCompile();
		$this->writer->startDocument('1.0', 'UTF-8');
	}

	/**
	 * メディアタイプを返す
	 *
	 * @access public
	 * @return string メディアタイプ
	 */
	public function getType () {
		return BSTypeList::getType('xml');
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
	 * 妥当な要素か？
	 *
	 * @access public
	 * @return boolean 妥当な要素ならTrue
	 */
	public function validate () {
		if (!$this->getContents()) {
			$this->error = '妥当なXML文書ではありません。';
			return false;
		}
		return true;
	}

	/**
	 * エラーメッセージを返す
	 *
	 * @access public
	 * @return string エラーメッセージ
	 */
	public function getError () {
		return $this->error;
	}
}

/* vim:set tabstop=4 ai: */
?>