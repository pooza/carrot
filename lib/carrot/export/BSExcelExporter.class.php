<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage export
 */

/**
 * Excelレンダラー
 *
 * Excel97（BIFF8）形式に対応。
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @link http://d.hatena.ne.jp/kent013/20080205/1202209327
 * @link http://chazuke.com/?p=93
 */
class BSExcelExporter implements BSExporter, BSRenderer {
	private $file;
	private $workbook;
	private $saved = false;
	private $row = 0;
	const SHEET_NAME = 'export';

	/**
	 * @access public
	 */
	public function __construct () {
		BSUtility::includeFile('pear/Spreadsheet/Excel/Writer.php');
		$this->workbook = new Spreadsheet_Excel_Writer($this->getFile()->getPath());
		$this->workbook->setVersion(8);
		$sheet = $this->workbook->addWorksheet(self::SHEET_NAME);
		$sheet->setInputEncoding('utf-8');
	}

	/**
	 * 一時ファイルを返す
	 *
	 * @access public
	 * @return BSFile 一時ファイル
	 */
	public function getFile () {
		if (!$this->file) {
			$this->file = BSFileUtility::createTemporaryFile('.xls');
			$this->file->setMode(0600);
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
		$col = 0;
		$sheets = $this->workbook->worksheets();
		$sheet = $sheets[0];
		foreach ($record as $key => $value) {
			$sheet->write($this->row, $col, $value);
			$col ++;
		}
		$this->row ++;
	}

	private function save () {
		if ($this->saved) {
			return;
		}
		$this->workbook->close();
		$this->saved = true;
	}

	/**
	 * 内容を返す
	 *
	 * @access public
	 * @return string CSVデータの内容
	 */
	public function getContents () {
		$this->save();
		return $this->getFile()->getContents();
	}

	/**
	 * 現在の行番号を返す
	 *
	 * @access public
	 * @return integer Excelでの行番号（内部表現+1）
	 */
	public function getRowNumber () {
		return $this->row + 1;
	}

	/**
	 * メディアタイプを返す
	 *
	 * @access public
	 * @return string メディアタイプ
	 */
	public function getType () {
		return BSMIMEType::getType('xls');
	}

	/**
	 * 出力内容のサイズを返す
	 *
	 * @access public
	 * @return integer サイズ
	 */
	public function getSize () {
		$this->save();
		return $this->getFile()->getSize();
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		$this->save();
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

	/**
	 * Excel式を生成して返す
	 *
	 * @access public
	 * @param string $source ソース文字列
	 * @return string Excel式
	 */
	public function createFormula ($source) {
		$formula = $source;
		$formula = mb_ereg_replace('@', $this->getRowNumber(), $formula);
		return $formula;
	}
}

/* vim:set tabstop=4: */
