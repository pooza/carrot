<?php
/**
 * @package org.carrot-framework
 * @subpackage log.logger.file
 */

/**
 * ログディレクトリ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSLogDirectory extends BSDirectory {

	/**
	 * @access public
	 * @param string $path ディレクトリのパス
	 */
	public function __construct ($path = null) {
		if (!$path) {
			$path = BSFileUtility::getPath('log');
		}
		parent::__construct($path);
		$this->setDefaultSuffix('.log');
	}

	/**
	 * サブディレクトリを持つか？
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
	public function getDefaultEntryClass () {
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

/* vim:set tabstop=4: */
