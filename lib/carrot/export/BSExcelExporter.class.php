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
	private $row = 1;

	/**
	 * @access public
	 */
	public function __construct () {
		BSUtility::includeFile('PHPExcel/PHPExcel.php');
		$this->workbook = new PHPExcel;
	}

	/**
	 * 一時ファイルを返す
	 *
	 * @access public
	 * @return BSFile 一時ファイル
	 */
	public function getFile () {
		if (!$this->file) {
			$this->file = BSFileUtility::createTemporaryFile('.xlsx');
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
		$sheet = $this->workbook->getActiveSheet();
		foreach ($record as $key => $value) {
			$sheet->setCellValueByColumnAndRow($col, $this->row, $value);
			$col ++;
		}
		$this->row ++;
	}

	private function save () {
		$writer = PHPExcel_IOFactory::createWriter($this->workbook, 'Excel2007');
		$writer->save($this->getFile()->getPath());
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
		return BSMIMEType::getType('xlsx');
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
}

/* vim:set tabstop=4: */
