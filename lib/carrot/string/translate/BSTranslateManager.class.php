<?php
/**
 * @package org.carrot-framework
 * @subpackage string.translate
 */

/**
 * 単語翻訳機能
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
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
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * @access public
	 */
	public function __clone () {
		throw new BadFunctionCallException(__CLASS__ . 'はコピーできません。');
	}

	/**
	 * 辞書ディレクトリを返す
	 *
	 * @access private
	 * @param BSDictionaryDirectory 辞書ディレクトリ
	 */
	private function getDirectory () {
		return BSFileUtility::getDirectory('dictionaries');
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
			$message = new BSStringFormat('辞書 "%s" は登録されていません。');
			$message[] = $name;
			throw new BSTranslateException($message);
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
		if (BSString::isBlank($string)) {
			return null;
		}
		if (BSString::isBlank($language)) {
			$language = $this->getLanguage();
		}
		foreach ($this->getDictionaryNames($name) as $name) {
			if ($dictionary = $this->dictionaries[$name]) {
				foreach ($this->getWords($string) as $word) {
					$answer = $dictionary->translate($word, $language);
					if ($answer !== null) {
						return $answer;
					}
				}
			}
		}
		if (BS_DEBUG) {
			$message = new BSStringFormat('"%s"の訳語が見つかりません。');
			$message[] = $string;
			throw new BSTranslateException($message);
		} else {
			return $string;
		}
	}

	private function getWords ($string) {
		return new BSArray(array(
			$string,
			BSString::underscorize($string),
			BSString::pascalize($string),
		));
	}

	private function getDictionaryNames ($name) {
		$names = new BSArray;
		$names[] = $name;
		$names[] = 'BSDictionaryFile.' . $name;
		$names->merge($this->dictionaries->getKeys());
		$names->uniquize();
		return $names;
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
		$language = BSString::toLower($language);
		if (!self::getLanguageNames()->isContain($language)) {
			$message = new BSStringFormat('言語コード"%s"が正しくありません。');
			$message[] = $language;
			throw new BSTranslateException($message);
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
		return self::getLanguages()->getFlipped();
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
			self::$languages = self::getInstance()->getHash(
				BSArray::explode(',', BS_LANGUAGES), 'en'
			);
		}
		return self::$languages;
	}
}

/* vim:set tabstop=4: */
