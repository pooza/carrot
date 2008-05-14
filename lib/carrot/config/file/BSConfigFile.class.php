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
	private $engine;

	/**
	 * 設定フォーマットを返す
	 *
	 * @access public
	 * @return BSConfigFormat 設定フォーマット
	 */
	public function getEngine () {
		if (!$this->engine) {
			switch ($this->getSuffix()) {
				case '.ini':
					$this->engine = new BSIniConfigFormat;
					break;
				case '.yaml':
					$this->engine = new BSYAMLConfigFormat;
					break;
				default:
					throw new BSConfigException(
						'%sはサポートされていないフォーマットです。',
						$this
					);
			}
			$this->engine->setContents($this->getContents());
		}
		return $this->engine;
	}

	/**
	 * 設定内容を返す
	 *
	 * @access public
	 * @return string[][] 設定ファイルの内容
	 */
	public function getResult () {
		if (!$this->config) {
			$this->config = $this->getEngine()->getResult();
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
	 * 利用可能な拡張子を返す
	 *
	 * @access public
	 * @return BSArray 拡張子
	 */
	public static function getSuffixes () {
		return new BSArray(array('.yaml', '.ini'));
	}
}

/* vim:set tabstop=4 ai: */
?>