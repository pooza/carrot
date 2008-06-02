<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage csv
 */

/**
 * CSVデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSCSVData implements BSRenderer {
	protected $contents;
	protected $records = array();
	protected $error;
	const FIELD_SEPARATOR = ',';
	const FIELD_SEPARATOR_TAG = '#COMMA#';
	const LINE_SEPARATOR = "\n";
	const LINE_SEPARATOR_TAG = '#CRLF#';

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param string $contents 
	 */
	public function __construct ($contents = null) {
		$this->setContents($contents);
	}

	/**
	 * 行をセットして、レコード配列を生成する
	 *
	 * @access public
	 * @param string[] $lines 
	 */
	public function setLines ($lines) {
		if (!BSArray::isArray($lines)) {
			$lines = array($lines);
		}
		$this->records = array();
		foreach ($lines as $line) {
			$this->addRecord(explode(self::FIELD_SEPARATOR, $line));
		}
	}

	/**
	 * レコードを追加する
	 *
	 * @access public
	 * @param string[] $record 
	 */
	public function addRecord ($record) {
		if (!BSArray::isArray($record)) {
			$record = array($record);
		} else if (!isset($record[0]) || ($record[0] == '')) {
			return;
		}
		$this->records[] = self::replaceTags($record);
		$this->contents = null;
	}

	/**
	 * 全てのレコードを返す
	 *
	 * @access public
	 * @return string[][] 全てのレコード
	 */
	public function getRecords () {
		if (!$this->records && $this->contents) {
			$this->setLines(explode(self::LINE_SEPARATOR, $this->contents));
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
			foreach ($this->getRecords() as $record) {
				$record = self::replaceSeparators($record);
				$this->contents .= implode(self::FIELD_SEPARATOR, $record);
				$this->contents .= self::LINE_SEPARATOR;
			}
		}
		return $this->contents;
	}

	/**
	 * 内容を設定する
	 *
	 * @access public
	 * @param string $contents CSVデータの内容
	 */
	public function setContents ($contents) {
		$this->contents = BSString::convertEncoding($contents);
		$this->records = array();
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
	 * セパレータタグを置換して返す
	 *
	 * @access public
	 * @param mixed $value 変換対象の文字列又は配列
	 * @return mixed 結果
	 * @static
	 */
	static public function replaceTags ($value) {
		if (BSArray::isArray($value)) {
			foreach ($value as &$item) {
				$item = self::replaceTags($item);
			}
		} else {
			if (preg_match('/^[\'"](.*)[\'"]$/', $value, $matches)) {
				$value = $matches[1];
			}

			$value = str_replace(self::FIELD_SEPARATOR_TAG, self::FIELD_SEPARATOR, $value);
			$value = str_replace(self::LINE_SEPARATOR_TAG, self::LINE_SEPARATOR, $value);
		}
		return $value;
	}

	/**
	 * セパレータを置換して返す
	 *
	 * @access public
	 * @param mixed $value 変換対象の文字列又は配列
	 * @return mixed 結果
	 * @static
	 */
	static public function replaceSeparators ($value) {
		if (BSArray::isArray($value)) {
			foreach ($value as &$item) {
				$item = self::replaceSeparators($item);
			}
		} else {
			$value = str_replace(self::FIELD_SEPARATOR, self::FIELD_SEPARATOR_TAG, $value);
			$value = str_replace(self::LINE_SEPARATOR, self::LINE_SEPARATOR_TAG, $value);
		}
		return $value;
	}

	/**
	 * メディアタイプを返す
	 *
	 * @access public
	 * @return string メディアタイプ
	 */
	public function getType () {
		return BSMediaType::getType('csv');
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

/* vim:set tabstop=4 ai: */
?>