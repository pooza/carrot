<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage translation
 */

/**
 * 単語翻訳機能
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSTranslator implements IteratorAggregate {
	private $language = 'ja';
	private $dictionaries;
	static private $instance;
	static private $languages;

	/**
	 * コンストラクタ
	 *
	 * @access private
	 */
	private function __construct () {
		$this->dictionaries = new BSArray;
		foreach ($this->getDirectory() as $dictionary) {
			if ($dictionary->getName() == 'carrot') {
				continue;
			}
			$this->register($dictionary);
		}
		$this->register($this->getDirectory()->getEntry('carrot'));
		$this->register(BSConstantHandler::getInstance());
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSTranslator インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (!self::$instance) {
			self::$instance = new BSTranslator;
		}
		return self::$instance;
	}

	/**
	 * ディープコピーを行う
	 *
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
	 * 辞書を登録する
	 *
	 * @access public
	 * @param BSDictionary 辞書
	 */
	public function register (BSDictionary $dictionary) {
		$this->dictionaries[] = $dictionary;
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
	 * @param string $language 言語
	 * @return string 訳語
	 */
	public function translate ($string, $language = null) {
		if (!$language) {
			$language = $this->getLanguage();
		}
		foreach ($this as $dictionary) {
			if ($answer = $dictionary->translate($string, $language)) {
				return $answer;
			}
		}
		if (BSController::getInstance()->isDebugMode()) {
			throw new BSTranslationException('"%s"の訳語が見つかりません。', $string);
		} else {
			return $string;
		}
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
			throw new BSTranslationException('言語コード"%s"が正しくありません。', $language);
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
			$hash[$word] = $this->translate($word, $language);
		}
		return $hash;
	}

	/**
	 * イテレータを返す
	 *
	 * @access public
	 * @return ArrayIterator 配列イテレータ
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
			self::$languages = self::getInstance()->getHash(
				BSArray::explode(',', APP_LANGUAGES), 'en'
			);
		}
		return self::$languages;
	}
}

/* vim:set tabstop=4 ai: */
?>