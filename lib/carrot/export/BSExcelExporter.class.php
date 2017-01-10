<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage export
 */

/**
 * Excelレンダラー
 *
 * Excel2007（xlsx）形式に対応。
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @link https://phpexcel.codeplex.com/
 */
class BSExcelExporter implements BSExporter, BSRenderer {
	private $file;
	private $workbook;
	private $row = 1;
	private $freezed = false;

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
			$cell = $sheet->getCellByColumnAndRow($col, $this->row);
			$cell->setValueExplicit($value);
			$cell->getStyle()->getAlignment()->setWrapText(true);
			$col ++;
		}
		$this->row ++;
	}

	private function save () {
		$writer = PHPExcel_IOFactory::createWriter($this->workbook, 'Excel2007');
		$writer->save($this->getFile()->getPath());
	}

	/**
	 * タイトル行を設定
	 *
	 * @access public
	 * @param BSArray $row タイトル行
	 */
	public function setHeader (BSArray $row) {
		if (!$this->freezed) {
			$this->addRecord($row);
			$this->workbook->getActiveSheet()->freezePaneByColumnAndRow(0, 2);
			$this->freezed = true;
		}
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
	 * @return integer Excelでの行番号
	 */
	public function getRowNumber () {
		return $this->row;
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
