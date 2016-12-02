<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage string.translate
 */

/**
 * 辞書ディレクトリ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSDictionaryDirectory extends BSDirectory {

	/**
	 * @access public
	 * @param string $path ディレクトリのパス
	 */
	public function __construct ($path = null) {
		if (!$path) {
			$path = BSFileUtility::getPath('dictionaries');
		}
		parent::__construct($path);
		$this->setDefaultSuffix('.csv');
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
		return 'BSDictionaryFile';
	}
}

/* vim:set tabstop=4: */
