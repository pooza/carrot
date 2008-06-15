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
 * @link http://project-p.jp/halt/kinowiki/php/Tips/csv 参考
 * @link http://www.din.or.jp/~ohzaki/perl.htm#CSV2Values 参考
 * @version $Id$
 */
class BSExcelCSVData extends BSHeaderCSVData {
	protected $fields = array();
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
	 * 行をセットして、レコード配列を生成する
	 *
	 * @access public
	 * @param string[] $lines 
	 */
	public function setLines ($lines) {
		$this->setFieldNames(explode(self::FIELD_SEPARATOR, $lines[0]));
		unset($lines[0]);

		$record = null;
		foreach ($lines as $line) {
			$record .= $line;
			preg_match_all('/"/', $record, $matched);
			if ((count($matched[0]) % 2) != 0) {
				continue;
			}
	
			preg_match_all('/"(.*?)"/', $record, $matched); 
			foreach ($matched[1] as $column) {
				$record = str_replace($column, self::replaceSeparators($column), $record);
			}   
			$fields = explode(self::FIELD_SEPARATOR, $record);
	
			foreach ($fields as &$field) {
				$field = rtrim($field);
				$field = preg_replace('/"(.*)"/', '\\1', $field);
				$field = str_replace('""', '"', $field);
			}   
	
			$this->addRecord($fields);
			$record = null;
		}   
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
		return BSString::convertEncoding(
			$this->getHeader() . $this->contents,
			$this->getEncoding()
		);
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
}

/* vim:set tabstop=4 ai: */
?>