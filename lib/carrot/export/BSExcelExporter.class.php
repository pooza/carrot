<?php
/**
 * @package org.carrot-framework
 * @subpackage export
 */

BSUtility::includeFile('write_excel/Worksheet.php');
BSUtility::includeFile('write_excel/Workbook.php');

/**
 * Excelレンダラー
 *
 * Excel95形式に対応。
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSExcelExporter implements BSExporter, BSRenderer {
	private $file;
	private $workbook;
	private $worksheet;
	private $line = 0;

	/**
	 * @access public
	 */
	public function __construct () {
		$this->workbook = new Workbook($this->getFile()->getPath());
		$this->worksheet = $this->workbook->add_worksheet('sheet1');
	}

	/**
	 * 一時ファイルを返す
	 *
	 * @access public
	 * @return BSFile 一時ファイル
	 */
	public function getFile () {
		if (!$this->file) {
			$this->file = BSFile::getTemporaryFile('.xls');
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
		$format = $this->workbook->addformat();
		$format->set_text_wrap();

		$col = 0;
		foreach ($record as $key => $value) {
			if ($value || BSNumeric::isZero($value)) {
				$value = BSString::convertEncoding($value, 'sjis-win', 'utf-8');
				$this->worksheet->write($this->line, $col, $value, $format);
			}
			$col ++;
		}
		$this->line ++;
	}

	/**
	 * 内容を返す
	 *
	 * @access public
	 * @return string CSVデータの内容
	 */
	public function getContents () {
		$this->workbook->close();
		return $this->getFile()->getContents();
	}

	/**
	 * メディアタイプを返す
	 *
	 * @access public
	 * @return string メディアタイプ
	 */
	public function getType () {
		return BSMediaType::getType('xls');
	}

	/**
	 * 出力内容のサイズを返す
	 *
	 * @access public
	 * @return integer サイズ
	 */
	public function getSize () {
		$this->workbook->close();
		return $this->getFile()->getSize();
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		$this->workbook->close();
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

/* vim:set tabstop=4: */
