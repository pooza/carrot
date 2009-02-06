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
	private $words;

	/**
	 * @access public
	 * @param string $path パス
	 */
	public function __construct ($path) {
		parent::__construct($path, new BSHeaderCSVData);
		$this->getEngine()->setEncoding('utf-8');
		$this->getEngine()->setRecordSeparator("\n");
	}

	/**
	 * 辞書の内容を返す
	 *
	 * @access public
	 * @return BSArray 辞書の内容
	 */
	public function getWords () {
		if (!$this->words) {
			$words = BSController::getInstance()->getAttribute(
				$this->getDictionaryName(),
				$this->getUpdateDate()
			);
			if (BSString::isBlank($words)) {
				$this->words = new BSArray;
				foreach ($this->getEngine()->getRecords() as $index => $record) {
					foreach ($record as $lang => $value) {
						if ($lang == 'label') {
							continue;
						}
						$this->words[$index . '_' . $lang] = $value;
					}
				}
				BSController::getInstance()->setAttribute(
					$this->getDictionaryName(),
					$this->words->getParameters()
				);
			} else {
				$this->words = new BSArray($words);
			}

		}
		return $this->words;
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
		return $this->getWords()->getParameter($label . '_' . $language);
	}

	/**
	 * 辞書の名前を返す
	 *
	 * @access public
	 * @return string 辞書の名前
	 */
	public function getDictionaryName () {
		return get_class($this) . '.' . $this->getBaseName();
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('辞書ファイル "%s"', $this->getPath());
	}
}

/* vim:set tabstop=4: */
