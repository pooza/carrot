<?php
/**
 * @package org.carrot-framework
 * @subpackage csv
 */

/**
 * ヘッダ付きCSVデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSHeaderCSVData extends BSCSVData {
	protected $fields;
	protected $hasRowID = true;

	/**
	 * @access public
	 * @param string $contents 
	 */
	public function __construct ($contents = null) {
		$this->fields = new BSArray;
		parent::__construct($contents);
	}

	/**
	 * 見出しを返す
	 *
	 * @access public
	 * @return BSArray 見出し
	 */
	public function getFieldNames () {
		return $this->fields;
	}

	/**
	 * 見出しを設定
	 *
	 * @access public
	 * @param BSArray $fields 見出し
	 */
	public function setFieldNames (BSArray $fields) {
		$fields = BSString::convertEncoding($fields);
		$fields = $this->trimRecord($fields);
		$this->fields = $fields;
	}

	/**
	 * 見出しをひとつ返す
	 *
	 * @access public
	 * @param integer $index 序数
	 * @return string 見出し
	 */
	public function getFieldName ($index) {
		return $this->fields[$index];
	}

	/**
	 * 見出し行を返す
	 *
	 * @access public
	 * @return string 見出し行
	 */
	public function getHeader () {
		return $this->getFieldNames()->join($this->getFieldSeparator())
			. $this->getRecordSeparator();
	}

	/**
	 * 行IDを持つか
	 *
	 * @access public
	 * @return boolean 行IDを持つならTrue 
	 */
	public function hasRowID () {
		return $this->hasRowID;
	}

	/**
	 * 行IDを持つかを設定
	 *
	 * @access public
	 * @param boolean $flag 行IDを持つならTrue 
	 */
	public function setHasRowID ($flag) {
		$this->hasRowID = $flag;
	}

	/**
	 * 行をセットして、レコード配列を生成
	 *
	 * @access public
	 * @param BSArray $lines 
	 */
	public function setLines (BSArray $lines) {
		$this->setFieldNames(BSString::explode($this->getFieldSeparator(), $lines->shift()));
		parent::setLines($lines);
	}

	/**
	 * レコードを追加
	 *
	 * @access public
	 * @param BSArray $record 
	 */
	public function addRecord (BSArray $record) {
		if (BSString::isBlank($record[$this->getFieldName(0)])) {
			$newRecord = new BSArray;
			for ($i = 0 ; $i < $this->getFieldNames()->count() ; $i ++) {
				$newRecord[$this->getFieldName($i)] = $record[$i];
			}
			$record = $newRecord;
		}

		if ($this->hasRowID()) {
			if (BSString::isBlank($record[$this->getFieldName(0)])) {
				return;
			}
			$this->records[$record[$this->getFieldName(0)]] = $this->trimRecord($record);
		} else {
			$this->records[] = $this->trimRecord($record);
		}
		$this->contents = null;
	}

	/**
	 * 内容を返す
	 *
	 * @access public
	 * @return string CSVデータの内容
	 */
	public function getContents () {
		return $this->getHeader() . parent::getContents();
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		if (!$this->getHeader()) {
			$this->error = '見出し行が正しくありません。';
			return false;
		}
		return parent::validate();
	}
}

/* vim:set tabstop=4: */
