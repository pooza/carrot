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
 * @version $Id$
 */
class BSLogDirectory extends BSDirectory {

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
	 * 最新のエントリーを返す
	 *
	 * @access public
	 * @param string $class エントリーのクラス名
	 * @return BSDirectoryEntry ログファイル
	 */
	public function getLatestEntry ($class = null) {
		if ($entries = $this->getEntryNames()) {
			if (!$class) {
				$class = $this->getDefaultEntryClassName();
			}
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
	 * サブディレクトリを持つか
	 *
	 * @access public
	 * @return boolean サブディレクトリを持つならTrue
	 */
	public function hasSubDirectory () {
		return false;
	}

	/**
	 * エントリーのクラス名を返す
	 *
	 * @access public
	 * @return string エントリーのクラス名
	 */
	public function getDefaultEntryClassName () {
		return 'BSLogFile';
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