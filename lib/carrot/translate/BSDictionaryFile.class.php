<?php
/**
 * @package org.carrot-framework
 * @subpackage translate
 */

/**
 * 辞書ファイル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSDictionaryFile extends BSCSVFile implements BSDictionary {
	private $contents = array();

	/**
	 * @access public
	 * @param string $path パス
	 */
	public function __construct ($path) {
		parent::__construct($path, new BSHeaderCSVData);
	}

	/**
	 * 辞書の内容を返す
	 *
	 * @access public
	 * @return string[][] 辞書の内容
	 */
	public function getContents () {
		if (!$this->contents) {
			$controller = BSController::getInstance();
			$name = get_class($this) . '.' . $this->getBaseName();
			$expire = $this->getUpdateDate();

			$this->contents = $controller->getAttribute($name, $expire);
			if ($this->contents === null) {
				$this->contents = $this->getEngine()->getRecords();
				$controller->setAttribute($name, $this->contents);
			}
		}
		return $this->contents;
	}

	/**
	 * 翻訳して返す
	 *
	 * @access public
	 * @param string $label ラベル
	 * @param string $language 言語
	 * @return string 翻訳された文字列
	 */
	public function translate ($label, $language) {
		$contents = $this->getContents();
		if (isset($contents[$label][$language])) {
			return $contents[$label][$language];
		}
	}

	/**
	 * 辞書の名前を返す
	 *
	 * @access public
	 * @return string 辞書の名前
	 */
	public function getDictionaryName () {
		return sprintf('%s.%s', get_class($this), $this->getBaseName());
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('辞書ファイル "%s"', $this->getPath());
	}
}

/* vim:set tabstop=4 ai: */
?>