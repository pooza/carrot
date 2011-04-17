<?php
/**
 * @package org.carrot-framework
 * @subpackage view.smarttag
 */

/**
 * スマートタグ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
abstract class BSSmartTag extends BSParameterHolder {
	private $useragent;
	protected $tag;
	protected $contents;

	/**
	 * @access public
	 * @param string[] $contents タグ
	 */
	public function __construct ($contents) {
		$this->contents = '[[' . $contents . ']]';
		$this->tag = self::unescape(BSString::explode(':', $contents));
	}

	/**
	 * パラメータを設定
	 *
	 * @access public
	 * @param string $name パラメータ名
	 * @param mixed $value 値
	 */
	public function setParameter ($name, $value) {
		switch ($name) {
			case 'useragent':
				return $this->setUserAgent($value);
			default:
				return parent::setParameter($name, $value);
		}
	}

	/**
	 * 対象UserAgentを返す
	 *
	 * @access public
	 * @return BSUserAgent 対象UserAgent
	 */
	public function getUserAgent () {
		if (!$this->useragent) {
			$this->setUserAgent(BSRequest::getInstance()->getUserAgent());
		}
		return $this->useragent;
	}

	/**
	 * 対象UserAgentを設定
	 *
	 * @access public
	 * @param BSUserAgent $useragent 対象UserAgent
	 */
	public function setUserAgent (BSUserAgent $useragent) {
		$this->useragent = $useragent;
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
	 * @return boolean 一致するならTrue
	 */
	public function isMatched () {
		return !BSString::isBlank($this->tag[0]) && ($this->tag[0] == $this->getTagName());
	}

	/**
	 * 引数をURLクエリーに変換して返す
	 *
	 * @access protected
	 * @return BSWWWFormRenderer
	 */
	protected function getQueryParameters () {
		$params = new BSWWWFormRenderer;
		$params->setContents(str_replace(';', '&', $this->tag[2]));
		return $params;
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

	/**
	 * スマートタグが含まれる文字列をパースする
	 *
	 * @access public
	 * @param string $text 置換対象文字列
	 * @param BSArray $tags 許可するタグ
	 * @param BSParameterHolder $params タグに載せるパラメータ
	 * @return string 置換された文字列
	 * @static
	 */
	static public function parse ($text, BSArray $tags = null, BSParameterHolder $params = null) {
		if (!$tags) {
			$tags = new BSArray;
		}
		$tags[] = 'Generic';
		$tags->uniquize();

		$text = self::escape($text);
		foreach (BSString::eregMatchAll('\\[\\[([^\\]]+)\\]\\]', $text) as $matches) {
			foreach ($tags as $tag) {
				$class = BSClassLoader::getInstance()->getClass($tag, 'Tag');
				$tag = new $class($matches[1]);
				if ($tag->isMatched()) {
					$tag->setParameters($params);
					$text = $tag->execute($text);
					break;
				}
			}
			$message = sprintf('[エラー: "%s" は不明なタグです。]', $matches[1]);
			$text = str_replace($matches[0], $message, $text);
		}
		$text = self::unescape($text);
		return $text;
	}

	/**
	 * メタ文字をエスケープ
	 *
	 * @access public
	 * @param string $text 置換対象文字列
	 * @return string 置換された文字列
	 * @static
	 */
	static public function escape ($value) {
		if (is_array($value) || ($value instanceof BSParameterHolder)) {
			foreach ($value as $key => $item) {
				$value[$key] = self::escape($item);
			}
		} else {
			$value = str_replace('\\:', '__COLON__', $value);
			$value = str_replace('\\[', '__RIGHT__', $value);
			$value = str_replace('\\]', '__LEFT__', $value);
		}
		return $value;
	}

	/**
	 * メタ文字のエスケープを戻す
	 *
	 * @access public
	 * @param string $text 置換対象文字列
	 * @return string 置換された文字列
	 * @static
	 */
	static public function unescape ($value) {
		if (is_array($value) || ($value instanceof BSParameterHolder)) {
			foreach ($value as $key => $item) {
				$value[$key] = self::escape($item);
			}
		} else {
			$value = str_replace('\\:', ':', $value);
			$value = str_replace('\\[', '[', $value);
			$value = str_replace('\\]', ']', $value);
			$value = str_replace('__COLON__', ':', $value);
			$value = str_replace('__RIGHT__', '[', $value);
			$value = str_replace('__LEFT__', ']', $value);
		}
		return $value;
	}
}

/* vim:set tabstop=4: */
