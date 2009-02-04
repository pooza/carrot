<?php
/**
 * @package org.carrot-framework
 * @subpackage csv
 */

/**
 * 標準CSVデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @link http://project-p.jp/halt/kinowiki/php/Tips/csv 参考
 * @link http://www.din.or.jp/~ohzaki/perl.htm#CSV2Values 参考
 * @version $Id$
 */
class BSStandardCSVData extends BSCSVData {
	protected $fields = array();
	const LINE_SEPARATOR = "\r\n";

	/**
	 * 行をセットして、レコード配列を生成
	 *
	 * @access public
	 * @param string[] $lines 
	 */
	public function setLines ($lines) {
		foreach ($lines as $line) {
			if (isset($record) && $record) {
				$record .= "\n" . $line;
			} else {
				$record = $line;
			}
			preg_match_all('/"/', $record, $matched);
			if ((count($matched[0]) % 2) != 0) {
				continue;
			}
			$record = BSString::convertEncoding($record);

			preg_match_all('/"(.*?)"/', $record, $matched); 
			foreach ($matched[1] as $column) {
				$record = str_replace($column, self::replaceSeparators($column), $record);
			}   
			$fields = explode(self::FIELD_SEPARATOR, $record);
	
			foreach ($fields as &$field) {
				$field = rtrim($field);
				$field = preg_replace('/"(.*)"/s', '\\1', $field);
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
					$field = sprintf('"%s"', str_replace('"', '""', $field));
				}
				$this->contents .= implode(self::FIELD_SEPARATOR, $record);
				$this->contents .= self::LINE_SEPARATOR;
			}
		}
		return BSString::convertEncoding($this->contents, $this->getEncoding());
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

/* vim:set tabstop=4: */
