<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage config
 */

/**
 * 設定マネージャ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSConfigManager {
	private $compilers;
	static private $instance;

	/**
	 * コンストラクタ
	 *
	 * @access private
	 */
	private function __construct () {
		$this->compilers = new BSArray;
		$this->compilers['config_compilers'] = new BSObjectRegisterConfigCompiler;

		$objects = array();
		require_once($this->compile('config_compilers'));
		$this->compilers->setParameters($objects);
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
			self::$instance = new BSConfigManager;
		}
		return self::$instance;
	}

	/**
	 * ディープコピーを行う
	 *
	 * @access public
	 */
	public function __clone () {
		throw new BSException('"%s"はコピー出来ません。', __CLASS__);
	}

	/**
	 * 設定ファイルをコンパイル
	 *
	 * @access public
	 * @param mixed $file BSFile又はファイル名
	 * @return string コンパイル済みキャッシュファイルのフルパス
	 */
	public function compile ($file) {
		if (!($file instanceof BSFile)) {
			$file = self::getConfigFile($file);
		}
		if (!$file->isReadable()) {
			throw new BSConfigException('%sが読めません。', $file);
		}

		$cache = self::getCacheFile($file);
		if (!$cache->isExists() || $cache->getUpdateDate()->isAgo($file->getUpdateDate())) {
			foreach ($this->compilers as $pattern => $compiler){
				if ($pattern == '.default') {
					$pattern = '/./'; //全てにマッチ
				} else {
					$pattern = '/' . preg_quote($pattern, '/') . '/';
				}
				if (preg_match($pattern, $file->getPath())) {
					$result = $compiler->execute($file);
					$cache->setContents($result);
					break;
				}
			}
		}
		return $cache->getPath();
	}

	/**
	 * キャッシュファイルを返す
	 *
	 * @access private
	 * @param BSConfigFile $file コンパイル対象設定ファイル
	 * @return BSFile キャッシュファイル
	 */
	static private function getCacheFile (BSConfigFile $file) {
		$name = $file->getDirectory()->getPath() . '/' . $file->getBaseName();
		$name = str_replace(BS_WEBAPP_DIR, '', $name);
		$name = str_replace(DIRECTORY_SEPARATOR, '.', $name);
		$name = preg_replace('/^\./', '', $name);

		//BSDirectoryFinderは使わない。
		return new BSFile(sprintf('%s/cache/%s.cache.php', BS_VAR_DIR, $name));
	}

	/**
	 * 設定ファイルを返す
	 *
	 * @access public
	 * @param string $name 設定ファイル名、但し拡張子は含まない
	 * @return BSConfigFile 設定ファイル
	 */
	static public function getConfigFile ($name) {
		if (!BSUtility::isPathAbsolute($name)) {
			$name = BS_WEBAPP_DIR . '/config/' . $name;
		}
		foreach (BSConfigFile::getSuffixes() as $suffix) {
			$file = new BSConfigFile($name . $suffix);
			if ($file->isExists()) {
				if (!$file->isReadable()) {
					throw new BSFileException('%sが読めません。', $file);
				}
				return $file;
			}
		}
	}
}

/* vim:set tabstop=4 ai: */
?>