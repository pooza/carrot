<?php
/**
 * @package org.carrot-framework
 * @subpackage config.file
 */

/**
 * 設定ファイル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSConfigFile extends BSFile {
	private $config = array();
	private $parser;
	private $cache;

	/**
	 * バイナリファイルか？
	 *
	 * @access public
	 * @return boolean バイナリファイルならTrue
	 */
	public function isBinary () {
		return false;
	}

	/**
	 * 設定パーサーを返す
	 *
	 * @access public
	 * @return BSConfigParser 設定パーサー
	 */
	public function getParser () {
		if (!$this->parser) {
			$this->parser = BSClassLoader::getInstance()->getObject(
				ltrim($this->getSuffix(), '.'),
				'ConfigParser'
			);
			$this->parser->setContents($this->getContents());
		}
		return $this->parser;
	}

	/**
	 * コンパイラを返す
	 *
	 * @access public
	 * @return BSConfigCompiler コンパイラ
	 */
	public function getCompiler () {
		return BSConfigManager::getInstance()->getCompiler($this);
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
	 * コンパイル
	 *
	 * @access public
	 * @return BSFile 設定キャッシュファイル
	 */
	public function compile () {
		$cache = $this->getCacheFile();
		if (!$cache->isExists() || $cache->getUpdateDate()->isPast($this->getUpdateDate())) {
			$cache->setContents($this->getCompiler()->execute($this));
		}
		return $cache;
	}

	/**
	 * キャッシュファイルを返す
	 *
	 * @access public
	 * @return BSFile キャッシュファイル
	 */
	public function getCacheFile () {
		if (!$this->cache) {
			$name = str_replace(BS_ROOT_DIR . '/', '', $this->getPath());
			$name = str_replace('/', '.', $name);
			$name = get_class($this) . '.' . $name . '.php';
			$this->cache = new BSFile(BS_VAR_DIR . '/cache/' . $name);
		}
		return $this->cache;
	}

	/**
	 * シリアライズ
	 *
	 * @access public
	 */
	public function serialize () {
		BSController::getInstance()->setAttribute($this, $this->getResult());
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('設定ファイル "%s"', $this->getShortPath());
	}
}

/* vim:set tabstop=4: */
