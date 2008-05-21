<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage config
 */

/**
 * 設定ファイル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSConfigFile extends BSFile {
	private $config = array();
	private $parser;

	/**
	 * 設定パーサーを返す
	 *
	 * @access public
	 * @return BSConfigParser 設定パーサー
	 */
	public function getParser () {
		if (!$this->parser) {
			if (!$name = self::getParserNames()->getParameter($this->getSuffix())) {
				throw new BSConfigException('%sはサポートされていないフォーマットです。', $this);
			}
			$this->parser = new $name;
			$this->parser->setContents($this->getContents());
		}
		return $this->parser;
	}

	/**
	 * 設定内容を返す
	 *
	 * @access public
	 * @return string[][] 設定ファイルの内容
	 */
	public function getResult () {
		if (!$this->config) {
			$this->config = $this->getParser()->getResult();
		}
		return $this->config;
	}

	/**
	 * 設定値を返す
	 *
	 * @access public
	 * @param string $name キー名
	 * @param string $section セクション名
	 * @return string 設定値
	 */
	public function getConfig ($name, $section = '') {
		$config = $this->getResult();
		if (isset($config[$section][$name])) {
			return $config[$section][$name];
		}
	}

	/**
	 * 基本情報を文字列で返す
	 *
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('設定ファイル "%s"', $this->getPath());
	}

	/**
	 * 利用可能な設定パーサーの名前を返す
	 *
	 * @access public
	 * @return BSArray 設定パーサーの名前
	 */
	static public function getParserNames () {
		$names = new BSArray();
		$names['.ini'] = 'BSIniConfigParser';
		$names['.yaml'] = 'BSYAMLConfigParser';
		return $names;
	}

	/**
	 * 利用可能な拡張子を返す
	 *
	 * @access public
	 * @return BSArray 拡張子
	 */
	static public function getSuffixes () {
		return self::getParserNames()->getKeys();
	}
}

/* vim:set tabstop=4 ai: */
?>