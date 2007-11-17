<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage translation
 */

/**
 * 簡易翻訳機能
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSTranslator {
	private $language = 'ja';
	private $dictionaries = array();
	private static $instance;
	private static $languages;

	/**
	 * コンストラクタ
	 *
	 * @access private
	 */
	private function __construct () {
		// コンストラクタからのインスタンス生成を禁止
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSTranslator インスタンス
	 * @static
	 */
	public static function getInstance () {
		if (!self::$instance) {
			self::$instance = new BSTranslator();
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
	 * 辞書配列を返す
	 *
	 * @access private
	 * @retnrn BSDictionary[] 辞書配列
	 */
	private function getDictionaries () {
		if (!$this->dictionaries) {
			foreach ($this->getDirectory() as $dictionary) {
				if ($dictionary->getName() == 'carrot') {
					continue;
				}
				$this->dictionaries[] = $dictionary;
			}
			$this->dictionaries[] = $this->getDirectory()->getEntry('carrot');
			$this->dictionaries[] = new BSConstantsDictionary();
		}
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
		foreach ($this->getDictionaries() as $dictionary) {
			if ($answer = $dictionary->translate($string, $language)) {
				return $answer;
			}
		}
		throw new BSTranslationException('"%s"の訳語が見つかりません。', $string);
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
		if (!in_array($language, self::getLanguageNames())) {
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
	 * @return string[] ハッシュ
	 */
	public function getHash ($words, $language = 'ja') {
		$hash = array();
		foreach ($words as $word) {
			$hash[$word] = $this->translate($word, $language);
		}
		return $hash;
	}

	/**
	 * 言語キー配列を出力
	 *
	 * @access public
	 * @return string[] 言語キー配列
	 * @static
	 */
	public static function getLanguageNames () {
		return array_keys(self::getLanguages());
	}

	/**
	 * 言語配列を返す
	 *
	 * @access public
	 * @return string[] 言語配列
	 * @static
	 */
	public static function getLanguages () {
		if (!self::$languages) {
			$translator = self::getInstance();
			self::$languages = $translator->getHash(explode(',', APP_LANGUAGES), 'en');
		}
		return self::$languages;
	}
}

/* vim:set tabstop=4 ai: */
?>