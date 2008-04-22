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
 * @version $Id: BSDirectoryIterator.class.php 5 2007-07-25 08:04:01Z pooza $
 */
class BSDirectoryIterator implements Iterator {
	private $directory;
	private $cursor = 0;

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param BSDirectory $directory ディレクトリ
	 */
	public function __construct (BSDirectory $directory) {
		$this->directory = $directory;
	}

	/**
	 * カーソルを巻き戻す
	 *
	 * @access public
	 */
	public function rewind () {
		$this->cursor = 0;
	}

	/**
	 * 現在のエントリーを返す
	 *
	 * @access public
	 * @return mixed ファイル又はディレクトリ
	 */
	public function current () {
		return $this->directory->getEntry($this->getEntryName());
	}

	/**
	 * 次のエントリーを返す
	 *
	 * @access public
	 * @return mixed ファイル又はディレクトリ
	 */
	public function next () {
		$this->cursor ++;
		return $this->directory->getEntry($this->getEntryName());
	}

	/**
	 * 現在のカーソル位置を返す
	 *
	 * @access public
	 * @return integer カーソル位置
	 */
	public function key () {
		return $this->cursor;
	}

	/**
	 * 現在のカーソル位置に正しいエントリーが存在するか
	 *
	 * @access public
	 * @return boolean 正しいエントリーが存在するならTrue
	 */
	public function valid () {
		$entries = $this->directory->getEntryNames();
		return isset($entries[$this->cursor]);
	}

	/**
	 * エントリーの名前を返す
	 *
	 * @access private
	 * @return string エントリーの名前
	 */
	private function getEntryName () {
		$entries = $this->directory->getEntryNames();
		if (isset($entries[$this->cursor])) {
			return $entries[$this->cursor];
		}
	}
}

/* vim:set tabstop=4 ai: */
?>