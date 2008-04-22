<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage log
 */

/**
 * ログディレクトリ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSLogDirectory.class.php 100 2007-11-18 08:26:50Z pooza $
 */
class BSLogDirectory extends BSDirectory {
	const DEFAULT_ENTRY_CLASS = 'BSLogFile';

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param string $path ディレクトリのパス
	 */
	public function __construct ($path = null) {
		if (!$path) {
			$path = BSController::getInstance()->getPath('log');
		}
		parent::__construct($path);
		$this->setDefaultSuffix('.log');
	}

	/**
	 * エントリーを返す
	 *
	 * @access public
	 * @param string $name エントリーの名前
	 * @param string $class エントリーのクラス名
	 * @return mixed ログファイル
	 */
	public function getEntry ($name, $class = self::DEFAULT_ENTRY_CLASS) {
		if (is_file($path = $this->getPath() . '/' . $name)) {
			return new $class($path);
		} else if (is_file($path .= $this->getDefaultSuffix())) {
			return new $class($path);
		}
	}

	/**
	 * 最新のエントリーを返す
	 *
	 * @access public
	 * @param string $class エントリーのクラス名
	 * @return BSDirectoryEntry ログファイル
	 */
	public function getLatestEntry ($class = self::DEFAULT_ENTRY_CLASS) {
		if ($entries = $this->getEntryNames()) {
			return $this->getEntry($entries[0], $class);
		}
	}


	/**
	 * 月毎にグループ化されたエントリー名を返す
	 *
	 * @access public
	 * @return string[][] エントリー名
	 */
	public function getDevidedEntryNames () {
		$names = array();
		foreach ($this->getEntryNames() as $name) {
			$date = new BSDate($name);
			$names[$date->format('Y-m')][$name] = $date->format('Y-m-d (ww)');
		}
		return $names;
	}

	/**
	 * ソート順を返す
	 *
	 * @access public
	 * @return string (ソート順 self::SORT_ASC | self::SORT_DESC)
	 */
	public function getSortOrder () {
		return self::SORT_DESC;
	}
}

/* vim:set tabstop=4 ai: */
?>