<?php
/**
 * @package org.carrot-framework
 * @subpackage translate
 */

/**
 * 単語翻訳機能
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSTranslateManager implements IteratorAggregate {
	private $language = 'ja';
	private $dictionaries;
	static private $instance;
	static private $languages;

	/**
	 * @access private
	 */
	private function __construct () {
		$this->dictionaries = new BSArray;
		foreach ($this->getDirectory() as $dictionary) {
			$this->register($dictionary);
		}
		$this->setDictionaryPriority('BSDictionaryFile.carrot', BSArray::POSITION_BOTTOM);
		$this->register(BSConstantHandler::getInstance());
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSTranslateManager インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (!self::$instance) {
			self::$instance = new BSTranslateManager;
		}
		return self::$instance;
	}

	/**
	 * @access public
	 */
	public function __clone () {
		throw new BSException('"%s"はコピー出来ません。', __CLASS__);
	}

	/**
	 * 辞書ディレクトリを返す
	 *
	 * @access private
	 * @param BSDictionaryDirectory 辞書ディレクトリ
	 */
	private function getDirectory () {
		return BSController::getInstance()->getDirectory('dictionaries');
	}

	/**
	 * 辞書を登録
	 *
	 * @access public
	 * @param BSDictionary 辞書
	 * @param boolean $priority 優先順位 (BSArray::POSITION_TOP|BSArray::POSITION_BOTTOM)
	 */
	public function register (BSDictionary $dictionary, $priority = BSArray::POSITION_BOTTOM) {
		$this->dictionaries->setParameter(
			$dictionary->getDictionaryName(),
			$dictionary,
			$priority
		);
	}

	/**
	 * 辞書の優先順位を設定
	 *
	 * @access public
	 * @param string $name 辞書の名前
	 * @param boolean $priority 優先順位 (BSArray::POSITION_TOP|BSArray::POSITION_BOTTOM)
	 */
	public function setDictionaryPriority ($name, $priority) {
		if (!$dictionary = $this->dictionaries[$name]) {
			throw new BSTranslateException('辞書 "%s" は登録されていません。', $name);
		}
		$this->dictionaries->removeParameter($name);
		$this->dictionaries->setParameter($name, $dictionary, $priority);
	}

	/**
	 * 辞書配列を返す
	 *
	 * @access private
	 * @retnrn BSDictionary[] 辞書配列
	 */
	private function getDictionaries () {
		return $this->dictionaries;
	}

	/**
	 * 単語を変換して返す
	 *
	 * @access public
	 * @param string $string 単語
	 * @param string $name 辞書の名前
	 * @param string $language 言語
	 * @return string 訳語
	 */
	public function translate ($string, $name = null, $language = null) {
		if (!$language) {
			$language = $this->getLanguage();
		}

		if ($dictionary = $this->dictionaries[$name]) {
			if ($answer = $dictionary->translate($string, $language)) {
				return $answer;
			}
		}

		foreach ($this as $dictionary) {
			if ($answer = $dictionary->translate($string, $language)) {
				return $answer;
			}
		}

		if (BSController::getInstance()->isDebugMode()) {
			throw new BSTranslateException('"%s"の訳語が見つかりません。', $string);
		} else {
			return $string;
		}
	}

	/**
	 * 単語を変換して返す
	 *
	 * translateのエイリアス
	 *
	 * @access public
	 * @param string $string 単語
	 * @param string $name 辞書の名前
	 * @param string $language 言語
	 * @return string 訳語
	 * @final
	 */
	final public function execute ($string, $name = null, $language = null) {
		return $this->translate($string, $name, $language);
	}

	/**
	 * 言語コードを返す
	 *
	 * @access public
	 * @return string 言語コード
	 */
	public function getLanguage () {
		return $this->language;
	}

	/**
	 * 言語コードを設定
	 *
	 * @access public
	 * @param string $language 言語コード
	 */
	public function setLanguage ($language) {
		$language = strtolower($language);
		if (!self::getLanguageNames()->isIncluded($language)) {
			throw new BSTranslateException('言語コード"%s"が正しくありません。', $language);
		}
		$this->language = $language;
	}

	/**
	 * ハッシュを返す
	 *
	 * @access public
	 * @param string[] $words 見出し語の配列
	 * @param string $language 言語
	 * @return BSArray ハッシュ
	 */
	public function getHash ($words, $language = 'ja') {
		$hash = new BSArray;
		foreach ($words as $word) {
			$hash[$word] = $this->execute($word, $language);
		}
		return $hash;
	}

	/**
	 * イテレータを返す
	 *
	 * @access public
	 * @return BSIterator イテレータ
	 */
	public function getIterator () {
		return $this->getDictionaries()->getIterator();
	}

	/**
	 * 言語キー配列を出力
	 *
	 * @access public
	 * @return BSArray 言語キー配列
	 * @static
	 */
	static public function getLanguageNames () {
		return self::getLanguages()->getKeys();
	}

	/**
	 * 言語配列を返す
	 *
	 * @access public
	 * @return BSArray 言語配列
	 * @static
	 */
	static public function getLanguages () {
		if (!self::$languages) {
			$languages = BSController::getInstance()->getConstant('LANGUAGES');
			self::$languages = self::getInstance()->getHash(
				BSArray::explode(',', $languages), 'en'
			);
		}
		return self::$languages;
	}
}

/* vim:set tabstop=4 ai: */
?>