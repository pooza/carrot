<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage file
 */

/**
 * ディレクトリイテレータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSDirectoryIterator extends BSIterator {
	private $directory;

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param BSDirectory $directory ディレクトリ
	 */
	public function __construct (BSDirectory $directory) {
		$this->directory = $directory;
		parent::__construct($directory->getEntryNames());
	}

	/**
	 * 現在のエントリーを返す
	 *
	 * @access public
	 * @return mixed ファイル又はディレクトリ
	 */
	public function current () {
		return $this->directory->getEntry(parent::current());
	}
}

/* vim:set tabstop=4 ai: */
?>