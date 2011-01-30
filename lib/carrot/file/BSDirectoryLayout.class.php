<?php
/**
 * @package org.carrot-framework
 * @subpackage file
 */

/**
 * ディレクトリレイアウト
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSDirectoryLayout extends BSParameterHolder {
	static private $instance;

	/**
	 * @access private
	 */
	private function __construct () {
		$configure = BSConfigManager::getInstance();

		$entries = new BSArray;
		$entries[] = 'carrot';
		$entries[] = 'application';
		$entries[] = BSController::getInstance()->getHost()->getName();
		foreach ($entries as $entry) {
			if ($file = BSConfigManager::getConfigFile('layout/' . $entry)) {
				foreach ($configure->compile($file) as $key => $values) {
					$this[$key] = new BSArray($values);
				}
			}
		}
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSDirectoryLayout インスタンス
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
	 * 特別なディレクトリを返す
	 *
	 * @access public
	 * @param string $name 名前
	 * @return BSDirectory ディレクトリ
	 */
	public function getDirectory ($name) {
		if (!$info = $this[$name]) {
			$message = new BSStringFormat('ディレクトリ "%s" が見つかりません。');
			$message[] = $name;
			throw new BSFileException($message);
		}
		if (!$info['instance']) {
			if (!BSString::isBlank($info['constant'])) {
				$dir = new BSDirectory(BSController::getInstance()->getAttribute($name . '_DIR'));
			} else if (!BSString::isBlank($info['name'])) {
				$dir = $this->getDirectory($info['parent'])->getEntry($info['name']);
			} else {
				$dir = $this->getDirectory($info['parent'])->getEntry($name);
			}
			if (!$dir || !$dir->isDirectory()) {
				$message = new BSStringFormat('ディレクトリ "%s" が見つかりません。');
				$message[] = $name;
				throw new BSFileException($message);
			}

			if (!BSString::isBlank($info['class'])) {
				$class = BSClassLoader::getInstance()->getClass($info['class']);
				$dir = new $class($dir->getPath());
			}
			if (!BSString::isBlank($info['suffix'])) {
				$dir->setDefaultSuffix($info['suffix']);
			}
			$info['instance'] = $dir;
		}
		return $info['instance'];
	}

	/**
	 * 特別なディレクトリのURLを返す
	 *
	 * @access public
	 * @param string $name ディレクトリの名前
	 * @return BSHTTPURL URL
	 */
	public function getURL ($name) {
		if (!$info = $this[$name]) {
			$message = new BSStringFormat('ディレクトリ "%s" が見つかりません。');
			$message[] = $name;
			throw new BSFileException($message);
		}
		if (!$info['url']) {
			if (BSString::isBlank($info['href'])) {
				$info['url'] = $this->getDirectory($name)->getURL();
			} else {
				$info['url'] = BSURL::getInstance();
				$info['url']['path'] = $info['href'];
			}
		}
		return clone $info['url'];
	}
}

/* vim:set tabstop=4: */
