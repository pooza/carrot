<?php
/**
 * @package org.carrot-framework
 * @subpackage export
 */

/**
 * Excelレンダラー
 *
 * Excel97形式に対応。
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSExcelExporter implements BSExporter, BSRenderer {
	private $file;
	private $engine;
	private $executed = false;
	private $line = 1;

	/**
	 * @access public
	 */
	public function __construct () {
		require_once 'PHPExcel.php';
		require_once 'PHPExcel/Writer/Excel5.php';
		$this->engine = new PHPExcel;
		$this->engine->setActiveSheetIndex(0);
		BSController::getInstance()->setTimeLimit(0);
		BSController::getInstance()->setMemoryLimit(-1);
	}

	/**
	 * 一時ファイルを返す
	 *
	 * @access public
	 * @return BSFile 一時ファイル
	 */
	public function getFile () {
		if (!$this->file) {
			$this->file = BSFileUtility::getTemporaryFile('xls');
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
		foreach ($record as $key => $value) {
			$position = PHPExcel_Cell::stringFromColumnIndex($col) . $this->line;
			$this->engine->getActiveSheet()->setCellValue($position, $value);
			$col ++;
		}
		$this->line ++;
	}

	private function execute () {
		if ($this->executed) {
			return;
		}
		$writer = new PHPExcel_Writer_Excel5($this->engine);
		$writer->save($this->getFile()->getPath());
		$this->executed = true;
	}

	/**
	 * 内容を返す
	 *
	 * @access public
	 * @return string CSVデータの内容
	 */
	public function getContents () {
		$this->execute();
		return $this->getFile()->getContents();
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
		$this->execute();
		return $this->getFile()->getSize();
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		$this->execute();
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
