<?php
/**
 * @package org.carrot-framework
 * @subpackage config.file
 */

/**
 * 設定ファイル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSConfigFile extends BSFile {
	private $config = array();
	private $parser;
	private $cache;

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
	 * コンパイル
	 *
	 * @access public
	 * @return string キャッシュファイルのフルパス
	 */
	public function compile () {
		$cache = $this->getCacheFile();
		$compiler = $this->getCompiler();
		if (!$cache->isExists() || $cache->getUpdateDate()->isPast($this->getUpdateDate())) {
			$cache->setContents($compiler->execute($this));
			$compiler->putLog($this->getLogMessage());
		}
		return $cache->getPath();
	}

	/**
	 * キャッシュファイルを返す
	 *
	 * @access public
	 * @return BSFile キャッシュファイル
	 */
	public function getCacheFile () {
		if (!$this->cache) {
			$name = $this->getDirectory()->getPath() . DIRECTORY_SEPARATOR . $this->getBaseName();
			$name = str_replace(BS_WEBAPP_DIR, '', $name);
			$name = str_replace(DIRECTORY_SEPARATOR, '.', $name);
			$name = preg_replace('/^\.+/', '', $name);
			$this->cache = new BSFile(BS_VAR_DIR . '/cache/' . $name . '.cache.php');
		}
		return $this->cache;
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('設定ファイル "%s"', $this->getPath());
	}

	/**
	 * ログメッセージを返す
	 *
	 * @access private
	 * @return string ログメッセージ
	 */
	private function getLogMessage () {
		return sprintf(
			'%sをコンパイルしました。 (%sB)',
			$this->getCacheFile()->getName(),
			BSNumeric::getBinarySize($this->getCacheFile()->getSize())
		);
	}

	/**
	 * 利用可能な設定パーサーの名前を返す
	 *
	 * @access public
	 * @return BSArray 設定パーサーの名前
	 */
	static public function getParserNames () {
		$names = new BSArray;
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

/* vim:set tabstop=4: */
