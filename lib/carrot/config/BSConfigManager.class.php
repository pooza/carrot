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
		$this->handlers['config_handlers.ini'] = new BSObjectRegisterConfigHandler;
		$this->handlers['config_handlers.ini']->initialize();

		$objects = array();
		require_once($this->compile('config_handlers.ini'));
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

	public function compile ($file) {
		if (!($file instanceof BSFile)) {
			if (!Toolkit::isPathAbsolute($file)) {
				$file = BS_WEBAPP_DIR . '/config/' . $file; //BSDirectoryFinderは使わない。
			}
			$file = new BSIniFile($file);
		}
		if (!$file->isReadable()) {
			throw new BSFileException('%s が読めません。', $file);
		}

		$cache = self::getCacheFile($file);
		if (!$cache->isExists() || $cache->getUpdateDate()->isAgo($file->getUpdateDate())) {
			foreach ($this->handlers as $pattern => $handler){
				$pattern = str_replace('*', '#WILDCARD#', $pattern);
				$pattern = preg_quote($pattern, '/');
				$pattern = str_replace('#WILDCARD#', '.*', $pattern);
				$pattern = '/' . $pattern . '/';
				if (preg_match($pattern, $file->getPath())) {
					$result = $handler->execute($file->getPath());
					$cache->setContents($result);
				}
			}
		}
		return $cache->getPath();
	}

	private static function getCacheFile (BSFile $file) {
		$name = $file->getDirectory()->getPath() . '/' . $file->getBaseName();
		$name = str_replace(BS_WEBAPP_DIR, '', $name);
		$name = str_replace(DIRECTORY_SEPARATOR, '.', $name);
		$name = preg_replace('/^\./', '', $name);
		$path = BS_VAR_DIR . '/cache/' . $name . '.cache.php'; //BSDirectoryFinderは使わない。
		return new BSFile($path);
	}
}

/* vim:set tabstop=4 ai: */
?>