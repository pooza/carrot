<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage csv
 */

/**
 * Excel形式ヘッダ付きCSVデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSExcelCSVData extends BSHeaderCSVData {
	private $fields = array();
	const LINE_SEPARATOR = "\r\n";

	/**
	 * 見出し行を返す
	 *
	 * @access public
	 * @return string 見出し行
	 */
	public function getHeader () {
		return implode(self::FIELD_SEPARATOR, $this->getFieldNames()) . self::LINE_SEPARATOR;
	}

	/**
	 * 見出しを設定する
	 *
	 * @access public
	 * @param string[] $fields 見出し
	 */
	public function setFieldNames ($fields) {
		// 誤認識対策
		if (strtolower($fields[0]) == 'id') {
			$fields[0] = '_ID';
		}

		parent::setFieldNames($fields);
	}

	/**
	 * 内容を返す
	 *
	 * @access public
	 * @return string CSVデータの内容
	 */
	public function getContents () {
		if (!$this->contents) {
			foreach ($this->getRecords() as $record) {
				foreach ($record as &$field) {
					// ダブルクォートをエスケープ
					$field = sprintf('"%s"', str_replace('"', '""', $field));
				}
				$this->contents .= implode(self::FIELD_SEPARATOR, $record);
				$this->contents .= self::LINE_SEPARATOR;
			}
		}
		return BSString::convertEncoding($this->getHeader() . $this->contents, 'sjis');
	}
}

/* vim:set tabstop=4 ai: */
?>