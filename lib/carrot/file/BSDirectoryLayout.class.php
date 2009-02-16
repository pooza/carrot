<?php
/**
 * @package org.carrot-framework
 * @subpackage file
 */

/**
 * ディレクトリレイアウト
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSDirectoryLayout {
	static private $instance;
	private $directories = array();

	/**
	 * @access private
	 */
	private function __construct () {
		require(BSConfigManager::getInstance()->compile('layout/carrot'));
		require(BSConfigManager::getInstance()->compile('layout/application'));

		$name = 'layout/' . BSController::getInstance()->getHost()->getName();
		if ($file = BSConfigManager::getConfigFile($name)) {
			require(BSConfigManager::getInstance()->compile($file));
		}
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSConsoleController インスタンス
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
		throw new BSSingletonException('"%s"はコピー出来ません。', __CLASS__);
	}

	/**
	 * 特別なディレクトリを返す
	 *
	 * @access public
	 * @param string $name 名前
	 * @return BSDirectory ディレクトリ
	 */
	public function getDirectory ($name) {
		if (!isset($this->directories[$name])) {
			throw new BSFileException('ディレクトリ "%s" が見つかりません。', $name);
		}
		if (!isset($this->directories[$name]['instance'])) {
			$this->directories[$name]['instance'] = $this->getDirectoryInstance($name);
		}
		return $this->directories[$name]['instance'];
	}

	/**
	 * 特別なディレクトリのインスタンスを生成
	 *
	 * @access private
	 * @param string $name 名前
	 */
	private function getDirectoryInstance ($name) {
		$params = $this->directories[$name];
		if (isset($params['constant'])) {
			$dir = new BSDirectory(BSController::getInstance()->getConstant($name . '_DIR'));
		} else if (isset($params['name'])) {
			$dir = $this->getDirectory($params['parent'])->getEntry($params['name']);
		} else {
			$dir = $this->getDirectory($params['parent'])->getEntry($name);
		}

		if (!$dir || !$dir->isDirectory()) {
			throw new BSFileException('ディレクトリ "%s" が見つかりません。', $name);
		}

		if (isset($params['class'])) {
			$class = BSClassLoader::getInstance()->getClassName($params['class']);
			$dir = new $class($dir->getPath());
		}
		if (isset($params['suffix'])) {
			$dir->setDefaultSuffix($params['suffix']);
		}

		return $dir;
	}

	/**
	 * 特別なディレクトリのパスを返す
	 *
	 * @access public
	 * @param string パス
	 */
	public function getPath ($name) {
		return $this->getDirectory($name)->getPath();
	}
}

/* vim:set tabstop=4: */
