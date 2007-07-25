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
class BSXMLDocument extends BSXMLElement implements BSViewEngine {
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