<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage csv
 */

/**
 * ヘッダ付きCSVデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSHeaderCSVData extends BSCSVData {
	protected $fields = array();

	/**
	 * 見出しを返す
	 *
	 * @access public
	 * @return string[] 見出し
	 */
	public function getFieldNames () {
		return $this->fields;
	}

	/**
	 * 見出しを設定する
	 *
	 * @access public
	 * @param string[] $fields 見出し
	 */
	public function setFieldNames ($fields) {
		$this->fields = $fields;
	}

	/**
	 * 見出しをひとつ返す
	 *
	 * @access public
	 * @param integer $index 序数、省略した時は最初の見出し（即ち主キー）
	 * @return string 見出し
	 */
	public function getFieldName ($index = 0) {
		if (isset($this->fields[$index])) {
			return $this->fields[$index];
		}
	}

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
	 * 行をセットして、レコード配列を生成する
	 *
	 * @access public
	 * @param string[] $lines 
	 */
	public function setLines ($lines) {
		$this->setFieldNames(explode(self::FIELD_SEPARATOR, $lines[0]));
		unset($lines[0]);
		parent::setLines($lines);
	}

	/**
	 * レコードを追加する
	 *
	 * @access public
	 * @param string[] $record 
	 */
	public function addRecord ($record) {
		$keys = array_keys($record);
		if (!$keys || ($record[$keys[0]] == '')) {
			return;
		}

		if (!isset($record[$this->getFieldName()])) {
			for ($i = 0 ; $i < count($this->getFieldNames()) ; $i ++) {
				$record[$this->getFieldName($i)] = $record[$i];
				unset($record[$i]);
			}
		} else {
			$recordOriginal = $record;
			$record = array();
			foreach ($this->getFieldNames() as $field) {
				if (isset($recordOriginal[$field])) {
					$record[$field] = $recordOriginal[$field];
				} else {
					$record[$field] = null;
				}
			}
		}

		$record = self::replaceTags($record);
		$this->records[$record[$this->getFieldName()]] = $record;
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

/* vim:set tabstop=4 ai: */
?>