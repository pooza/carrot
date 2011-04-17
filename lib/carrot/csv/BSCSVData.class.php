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
 */
class BSCSVData implements BSTextRenderer, IteratorAggregate, Countable {
	protected $contents;
	protected $records;
	protected $encoding = 'sjis-win';
	protected $recordSeparator = "\r\n";
	protected $fieldSeparator = ',';
	protected $error;
	private $commaSearchPattern;
	const COMMA_TAG = '_COMMA_';

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
		$lines = BSString::convertEncoding($lines);
		$this->records = new BSArray;
		foreach ($lines as $line) {
			if (isset($record) && $record) {
				$record .= "\n" . $line;
			} else {
				$record = $line;
			}

			if (!$this->isCompleteRecord($record)) {
				continue;
			}

			$record = $this->escapeCommaValue($record);
			$record = BSString::explode($this->getFieldSeparator(), $record);
			$record = $this->unescapeCommaValue($record);
			$this->addRecord($record);
			$record = null;
		}
	}

	private function isCompleteRecord ($record) {
		return (BSString::eregMatchAll('"', $record)->count() % 2) == 0;
	}

	private function escapeCommaValue ($record) {
		while (mb_ereg($this->getCommaSearchPattern(), $record, $matches)) {
			$record = str_replace(
				$matches[1],
				str_replace($this->getFieldSeparator(), self::COMMA_TAG, $matches[1]),
				$record
			);
		}
		return $record;
	}

	private function unescapeCommaValue (BSArray $record) {
		foreach ($record as $key => $value) {
			$record[$key] = str_replace(self::COMMA_TAG, $this->getFieldSeparator(), $value);
		}
		return $record;
	}

	private function getCommaSearchPattern () {
		if (!$this->commaSearchPattern) {
			$pattern = '_COMMA_"(([^"_COMMA_]*_COMMA_)+)([^"_COMMA_]*)?"';
			$pattern = str_replace(self::COMMA_TAG, $this->getFieldSeparator(), $pattern);
			$this->commaSearchPattern = $pattern;
		}
		return $this->commaSearchPattern;
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
	 * @access protected
	 * @param BSArray $record レコード
	 * @return BSArray トリミングされたレコード
	 */
	protected function trimRecord (BSArray $record) {
		foreach ($record as $key => $field) {
			$field = rtrim($field);
			$field = mb_ereg_replace('"(.*)"', '\\1', $field, 'm');
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
			$contents = new BSArray;
			foreach ($this->getRecords() as $key => $record) {
				foreach ($record as $key => $field) {
					$field = '"' . str_replace('"', '""', $field) . '"';
					$record[$key] = $field;
				}
				$contents[] = $record->join($this->getFieldSeparator());
			}
			$contents = $contents->join($this->getRecordSeparator());
			$contents = BSString::convertEncoding($contents, $this->getEncoding());
			$contents = BSString::convertLineSeparator($contents, $this->getRecordSeparator());
			$this->contents = $contents;
		}
		return $this->contents;
	}

	/**
	 * 内容を設定
	 *
	 * @access public
	 * @param string $contents CSVデータの内容
	 */
	public function setContents ($contents) {
		$contents = BSString::convertLineSeparator($contents, $this->getRecordSeparator());
		$contents = BSString::convertEncoding($contents, $this->getEncoding());
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
	 * @access public
	 * @return integer レコード数
	 */
	public function count () {
		return $this->records->count();
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		if (!($this->getRecords() instanceof BSArray)) {
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

	/**
	 * @access public
	 * @return BSIterator イテレータ
	 */
	public function getIterator () {
		return new BSIterator($this->getRecords());
	}
}

/* vim:set tabstop=4: */
