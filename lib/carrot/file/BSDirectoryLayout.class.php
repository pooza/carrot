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
	private $config;
	static private $instance;

	/**
	 * @access private
	 */
	private function __construct () {
		$this->config = new BSArray;
		$entries = new BSArray;
		$entries[] = 'carrot';
		$entries[] = 'application';
		$entries[] = BSController::getInstance()->getHost()->getName();
		foreach ($entries as $entry) {
			if ($file = BSConfigManager::getConfigFile('layout/' . $entry)) {
				foreach (BSConfigManager::getInstance()->compile($file) as $key => $values) {
					$this->config[$key] = new BSArray($values);
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

	private function getEntry ($name) {
		if (!$info = $this->config[$name]) {
			$message = new BSStringFormat('ディレクトリ "%s" が見つかりません。');
			$message[] = $name;
			throw new BSFileException($message);
		}
		return $info;
	}

	/**
	 * 設定を全て返す
	 *
	 * @access public
	 * @return BSArray 全ての設定
	 */
	public function getEntries () {
		return $this->config;
	}

	/**
	 * ディレクトリを返す
	 *
	 * @access public
	 * @param string $name ディレクトリ名
	 * @return BSDirectory ディレクトリ
	 */
	public function getParameter ($name) {
		if (!$this->hasParameter($name) && ($info = $this->getEntry($name))) {
			$controller = BSController::getInstance();
			if (!!$info['constant']) {
				$dir = new BSDirectory($controller->getAttribute($name . '_DIR'));
			} else if (!!$info['platform']) {
				$dir = $controller->getPlatform()->getDirectory($name);
			} else if (!BSString::isBlank($info['name'])) {
				$dir = $this[$info['parent']]->getEntry($info['name']);
			} else {
				$dir = $this[$info['parent']]->getEntry($name);
			}

			if ($dir instanceof BSDirectory) {
				if (!BSString::isBlank($info['class'])) {
					$class = BSClassLoader::getInstance()->getClass($info['class']);
					$dir = new $class($dir->getPath());
				}
				if (!BSString::isBlank($info['suffix'])) {
					$dir->setDefaultSuffix($info['suffix']);
				}
				$this->params[$name] = $dir;
			}
		}
		return $this->params[$name];
	}

	/**
	 * 特別なディレクトリのURLを返す
	 *
	 * @access public
	 * @param string $name ディレクトリの名前
	 * @return BSHTTPURL URL
	 */
	public function createURL ($name) {
		if (($info = $this->getEntry($name)) && BSString::isBlank($info['url'])) {
			if (BSString::isBlank($info['href'])) {
				$info['url'] = $this[$name]->getURL();
			} else {
				$info['url'] = BSURL::create();
				$info['url']['path'] = $info['href'];
			}
		}
		return clone $info['url'];
	}
}

/* vim:set tabstop=4: */
