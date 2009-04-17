<?php
/**
 * @package org.carrot-framework
 * @subpackage view.smarttag
 */

/**
 * スマートタグ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
abstract class BSSmartTag extends BSParameterHolder {
	protected $tag;
	protected $contents;
	private $params;

	/**
	 * @access public
	 * @param string[] $contents タグ
	 */
	public function __construct ($contents) {
		$this->contents = '[[' . $contents . ']]';
		$this->tag = BSString::explode(':', $contents);
	}

	/**
	 * 完全なタグを返す
	 *
	 * @access public
	 * @return string 完全なタグ
	 */
	protected function getContents () {
		return $this->contents;
	}

	/**
	 * 一致するか
	 *
	 * @access public
	 * @return string 完全なタグ
	 */
	public function isMatched () {
		return isset($this->tag[0]) && ($this->tag[0] == $this->getTagName());
	}

	/**
	 * タグ名を返す
	 *
	 * @access public
	 * @return string タグ名
	 * @abstract
	 */
	abstract public function getTagName ();

	/**
	 * 置換して返す
	 *
	 * @access public
	 * @param string $body 置換対象文字列
	 * @return string 置換された文字列
	 * @abstract
	 */
	abstract public function execute ($body);
}

/* vim:set tabstop=4: */
