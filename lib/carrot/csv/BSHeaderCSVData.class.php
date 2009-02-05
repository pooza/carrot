<?php
/**
 * @package org.carrot-framework
 * @subpackage csv
 */

/**
 * ヘッダ付きCSVデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSHeaderCSVData extends BSCSVData {
	protected $fields;

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
	public function setFieldNames (BSarray $fields) {
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
		$header = $this->getFieldNames()->join($this->getFieldSeparator());
		$header .= $this->getRecordSeparator();
		return $header;
	}

	/**
	 * 行をセットして、レコード配列を生成
	 *
	 * @access public
	 * @param BSArray $lines 
	 */
	public function setLines (BSArray $lines) {
		$this->setFieldNames(BSString::explode($this->getFieldSeparator(), $lines[0]));
		$lines->removeParameter(0);
		parent::setLines($lines);
	}

	/**
	 * レコードを追加
	 *
	 * @access public
	 * @param BSArray $record 
	 */
	public function addRecord (BSArray $record) {
		if (!$record[$this->getFieldName(0)]) {
			for ($i = 0 ; $i < $this->getFieldNames()->count() ; $i ++) {
				$record[$this->getFieldName($i)] = $record[$i];
				$record->removeParameter($i);
			}
		}
		parent::addRecord($record);
	}

	/**
	 * レコードは空か？
	 *
	 * @access public
	 * @param BSArray $record レコード
	 * @return boolean 空ならTrue
	 */
	protected function isEmptyRecord ($record) {
		$value = $record[$this->getFieldName(0)];
		return (!$value && !BSNumeric::isZero($value));
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
