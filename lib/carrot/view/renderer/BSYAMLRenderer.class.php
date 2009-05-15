<?php
/**
 * @package org.carrot-framework
 * @subpackage view.renderer
 */

/**
 * YAMLレンダラー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSYAMLRenderer implements BSRenderer {

	/**
	 * 出力内容を返す
	 *
	 * @access public
	 */
	public function getContents () {
		return $this->contents;
	}

	/**
	 * 出力内容を設定
	 *
	 * @param string $contents 出力内容
	 * @access public
	 */
	public function setContents ($contents) {
		if (BSArray::isArray($contents)) {
			$contents = new BSArray($contents);
			$parser = new BSYAMLConfigParser;
			$parser->setResult($contents);
			$contents = $parser->getContents();
		}
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
		return BSMIMEType::getType('yaml');
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
}

/* vim:set tabstop=4: */
