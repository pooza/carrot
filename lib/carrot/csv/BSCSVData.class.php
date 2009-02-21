<?php
/**
 * @package org.carrot-framework
 * @subpackage csv
 */

/**
 * CSVデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @link http://project-p.jp/halt/kinowiki/php/Tips/csv 参考
 * @link http://www.din.or.jp/~ohzaki/perl.htm#CSV2Values 参考
 * @version $Id$
 */
class BSCSVData implements BSTextRenderer {
	protected $contents;
	protected $records;
	protected $encoding = 'sjis-win';
	protected $recordSeparator = "\r\n";
	protected $fieldSeparator = ',';
	protected $error;

	/**
	 * @access public
	 * @param string $contents 
	 */
	public function __construct ($contents = null) {
		$this->records = new BSArray;
		$this->setContents($contents);
	}

	/**
	 * 行をセットして、レコード配列を生成
	 *
	 * @access public
	 * @param BSArray $lines 
	 */
	public function setLines (BSArray $lines) {
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

			$fields = BSString::explode(
				$this->getFieldSeparator(),
				BSString::convertEncoding($record)
			);
			$record = null;
			$this->addRecord($fields);
		}
	}

	/**
	 * レコードを追加
	 *
	 * @access public
	 * @param BSArray $record 
	 */
	public function addRecord (BSArray $record) {
		if (BSString::isBlank($record[0])) {
			return;
		}
		$this->records[] = $this->trimRecord($record);
		$this->contents = null;
	}

	/**
	 * レコードをトリミング
	 *
	 * @access public
	 * @param BSArray $record レコード
	 * @return BSArray トリミングされたレコード
	 */
	protected function trimRecord (BSArray $record) {
		foreach ($record as $key => $field) {
			$field = rtrim($field);
			$field = preg_replace('/"(.*)"/s', '\\1', $field);
			$field = str_replace('""', '"', $field);
			$record[$key] = $field;
		}
		return $record;
	}

	/**
	 * 全てのレコードを返す
	 *
	 * @access public
	 * @return string[][] 全てのレコード
	 */
	public function getRecords () {
		if (!$this->records->count() && $this->contents) {
			$this->setLines(BSString::explode($this->getRecordSeparator(), $this->contents));
		}
		return $this->records;
	}

	/**
	 * 内容を返す
	 *
	 * @access public
	 * @return string CSVデータの内容
	 */
	public function getContents () {
		if (!$this->contents) {
			foreach ($this->getRecords() as $key => $record) {
				foreach ($record as $key => $field) {
					$field = '"' . str_replace('"', '""', $field) . '"';
					$record[$key] = $field;
				}
				$this->contents .= $record->join($this->getFieldSeparator());
				$this->contents .= $this->getRecordSeparator();
			}
		}
		return BSString::convertEncoding($this->contents, $this->getEncoding());
	}

	/**
	 * 内容を設定
	 *
	 * @access public
	 * @param string $contents CSVデータの内容
	 */
	public function setContents ($contents) {
		$contents = BSString::convertLineSeparator($contents, $this->getRecordSeparator());
		$contents = BSString::convertEncoding($contents);
		$this->contents = $contents;
		$this->records = new BSArray;
	}

	/**
	 * 出力内容のサイズを返す
	 *
	 * @access public
	 * @return integer サイズ
	 */
	public function getSize () {
		return strlen($this->getContents());
	}

	/**
	 * メディアタイプを返す
	 *
	 * @access public
	 * @return string メディアタイプ
	 */
	public function getType () {
		return BSMIMEType::getType('csv');
	}

	/**
	 * エンコードを返す
	 *
	 * @access public
	 * @return string PHPのエンコード名
	 */
	public function getEncoding () {
		return $this->encoding;
	}

	/**
	 * エンコードを設定
	 *
	 * @access public
	 * @param string $encoding PHPのエンコード名
	 */
	public function setEncoding ($encoding) {
		$this->encoding = $encoding;
	}

	/**
	 * レコード区切りを返す
	 *
	 * @access public
	 * @return string レコード区切り
	 */
	public function getRecordSeparator () {
		return $this->recordSeparator;
	}

	/**
	 * レコード区切りを設定
	 *
	 * @access public
	 * @param string $recordSeparator レコード区切り
	 */
	public function setRecordSeparator ($separator) {
		$this->recordSeparator = $separator;
	}

	/**
	 * フィールド区切りを返す
	 *
	 * @access public
	 * @return string フィールド区切り
	 */
	public function getFieldSeparator () {
		return $this->fieldSeparator;
	}

	/**
	 * フィールド区切りを設定
	 *
	 * @access public
	 * @param string $fieldSeparator フィールド区切り
	 */
	public function setFieldSeparator ($separator) {
		$this->fieldSeparator = $separator;
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		if (!BSArray::isArray($this->getRecords())) {
			$this->error = 'データ配列が正しくありません。';
			return false;
		}
		return true;
	}

	/**
	 * エラーメッセージを返す
	 *
	 * @access public
	 * @return string エラーメッセージ
	 */
	public function getError () {
		return $this->error;
	}
}

/* vim:set tabstop=4: */
