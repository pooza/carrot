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
 * @version $Id$
 */
class BSXMLDocument extends BSXMLElement implements BSTextRenderer {
	private $error;

	/**
	 * メディアタイプを返す
	 *
	 * @access public
	 * @return string メディアタイプ
	 */
	public function getType () {
		return BSMediaType::getType('xml');
	}

	/**
	 * エンコードを返す
	 *
	 * @access public
	 * @return string PHPのエンコード名
	 */
	public function getEncoding () {
		return 'utf-8';
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
	 * 内容をXMLで返す
	 *
	 * @access public
	 * @return string XML文書
	 */
	public function getContents () {
		return '<?xml version="1.0"?>' . parent::getContents();
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