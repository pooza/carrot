<?php
/**
 * @package org.carrot-framework
 * @subpackage csv
 */

/**
 * ファイル追記型CSVレンダラー
 *
 * パースの必要がなく、大量のCSVデータを出力するケースで使用する。
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSCSVWriter implements BSTextRenderer {
	private $file;
	const LINE_SEPARATOR = "\r\n";

	/**
	 * 一時ファイルを返す
	 *
	 * @access public
	 * @return BSFile 一時ファイル
	 */
	public function getFile () {
		if (!$this->file) {
			$this->file = BSFile::getTemporaryFile('.csv');
		}
		return $this->file;
	}

	/**
	 * レコードを追加
	 *
	 * @access public
	 * @param BSArray $record レコード
	 */
	public function addRecord (BSArray $record) {
		$values = new BSArray;
		foreach ($record as $key => $value) {
			$value = BSString::convertEncoding($value, $this->getEncoding(), 'utf-8');
			$value = str_replace("\n", self::LINE_SEPARATOR, $value);
			$value = str_replace('"', '""', $value);
			$value = '"' . $value . '"';
			$values[$key] = $value;
		}

		if (!$this->getFile()->isOpened()) {
			$this->getFile()->open('a');
		}
		$this->getFile()->putLine($values->join(','), self::LINE_SEPARATOR);
	}

	/**
	 * 内容を返す
	 *
	 * @access public
	 * @return string CSVデータの内容
	 */
	public function getContents () {
		if ($this->getFile()->isOpened()) {
			$this->getFile()->close();
		}
		return $this->getFile()->getContents();
	}

	/**
	 * メディアタイプを返す
	 *
	 * @access public
	 * @return string メディアタイプ
	 */
	public function getType () {
		return BSMediaType::getType('csv');
	}

	/**
	 * 出力内容のサイズを返す
	 *
	 * @access public
	 * @return integer サイズ
	 */
	public function getSize () {
		return $this->getFile()->getSize();
	}

	/**
	 * エンコードを返す
	 *
	 * @access public
	 * @return string PHPのエンコード名
	 */
	public function getEncoding () {
		return 'sjis-win';
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		return true;
	}

	/**
	 * エラーメッセージを返す
	 *
	 * @access public
	 * @return string エラーメッセージ
	 */
	public function getError () {
		return null;
	}
}

/* vim:set tabstop=4 ai: */
?>