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
	private $handlers;
	private static $instance;

	/**
	 * コンストラクタ
	 *
	 * @access private
	 */
	private function __construct () {
		$this->handlers = new BSArray;
		$this->handlers['config_handlers'] = new BSObjectRegisterConfigHandler;

		$objects = array();
		require_once($this->compile('config_handlers'));
		$this->handlers->setParameters($objects);
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSSerializeHandler インスタンス
	 * @static
	 */
	public static function getInstance () {
		if (!self::$instance) {
			self::$instance = new BSConfigManager();
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
			foreach ($this->handlers as $pattern => $handler){
				$pattern = str_replace('*', '#WILDCARD#', $pattern);
				$pattern = preg_quote($pattern, '/');
				$pattern = str_replace('#WILDCARD#', '.*', $pattern);
				$pattern = '/' . $pattern . '/';
				if (preg_match($pattern, $file->getPath())) {
					$result = $handler->execute($file);
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
	private static function getCacheFile (BSConfigFile $file) {
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
	public static function getConfigFile ($name) {
		if (!Toolkit::isPathAbsolute($name)) {
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