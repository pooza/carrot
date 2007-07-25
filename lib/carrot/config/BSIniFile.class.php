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
 * @version $Id: BSIniFile.class.php 333 2007-06-08 05:48:46Z pooza $
 */
class BSIniFile extends BSFile {
	private $config = array();

	/**
	 * 内容を返す
	 *
	 * @access public
	 * @return string[][] 設定ファイルの内容
	 */
	public function getContents () {
		if (!$this->config) {
			$this->config = parse_ini_file($this->getPath(), true);
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
		$config = $this->getContents();
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
}

/* vim:set tabstop=4 ai: */
?>