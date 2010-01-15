<?php
/**
 * @package org.carrot-framework
 * @subpackage config
 */

/**
 * 設定マネージャ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSConfigManager {
	private $compilers;
	static private $instance;

	/**
	 * @access private
	 */
	private function __construct () {
		$file = self::getConfigFile('config_compilers', 'BSRootConfigFile');
		$this->compilers = new BSArray($this->compile($file));
		$this->compilers[] = new BSDefaultConfigCompiler(array('pattern' => '.'));;
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSSerializeHandler インスタンス
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
		throw new BSSingletonException(__CLASS__ . 'はコピーできません。');
	}

	/**
	 * 設定ファイルをコンパイル
	 *
	 * @access public
	 * @param mixed $file BSFile又はファイル名
	 * @return mixed 設定ファイルからの戻り値
	 */
	public function compile ($file) {
		if (!($file instanceof BSFile)) {
			$file = self::getConfigFile($file);
		}
		if (!$file->isReadable()) {
			throw new BSConfigException($file . 'が読めません。');
		}
		return require($file->compile()->getPath());
	}

	/**
	 * 設定ファイルに適切なコンパイラを返す
	 *
	 * @access public
	 * @param BSConfigFile $file 設定ファイル
	 * @return BSConfigCompiler 設定コンパイラ
	 */
	public function getCompiler (BSConfigFile $file) {
		foreach ($this->compilers as $compiler) {
			if (mb_ereg($compiler['pattern'], $file->getPath())) {
				return $compiler;
			}
		}
		throw new BSConfigException($file . 'の設定コンパイラがありません。');
	}

	/**
	 * 設定ファイルを返す
	 *
	 * @access public
	 * @param string $name 設定ファイル名、但し拡張子は含まない
	 * @param string $class 設定ファイルのクラス名
	 * @return BSConfigFile 設定ファイル
	 */
	static public function getConfigFile ($name, $class = 'BSConfigFile') {
		if (!BSUtility::isPathAbsolute($name)) {
			$name = BS_WEBAPP_DIR . '/config/' . $name;
		}
		$class = BSClassLoader::getInstance()->getClass($class);
		foreach (array('.yaml', '.ini') as $suffix) {
			$file = new $class($name . $suffix);
			if ($file->isExists()) {
				if (!$file->isReadable()) {
					throw new BSConfigException($file . 'が読めません。');
				}
				return $file;
			}
		}
	}
}

/* vim:set tabstop=4: */
