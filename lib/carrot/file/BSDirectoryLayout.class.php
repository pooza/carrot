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
	private $directories;

	/**
	 * @access private
	 */
	private function __construct () {
		$configure = BSConfigManager::getInstance();
		$this->directories = new BSArray;

		$entries = new BSArray;
		$entries[] = 'carrot';
		$entries[] = 'application';
		$entries[] = BSController::getInstance()->getHost()->getName();
		foreach ($entries as $entry) {
			if ($file = BSConfigManager::getConfigFile('layout/' . $entry)) {
				foreach ($configure->compile($file) as $key => $values) {
					$this->directories[$key] = new BSArray($values);
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
		throw new BSSingletonException(__CLASS__ . 'はコピーできません。');
	}

	/**
	 * 特別なディレクトリを返す
	 *
	 * @access public
	 * @param string $name 名前
	 * @return BSDirectory ディレクトリ
	 */
	public function getDirectory ($name) {
		if (!$info = $this->directories[$name]) {
			throw new BSFileException('ディレクトリ "%s" が見つかりません。', $name);
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
				throw new BSFileException('ディレクトリ "%s" が見つかりません。', $name);
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
}

/* vim:set tabstop=4: */
