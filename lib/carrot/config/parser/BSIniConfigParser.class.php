<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage config.parser
 */

/**
 * INI設定パーサー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSIniConfigParser implements BSConfigParser {
	private $contents;
	private $result;

	/**
	 * 変換前の設定内容を返す
	 *
	 * @access public
	 * @return string 設定内容
	 */
	public function getContents () {
		if (!$this->contents) {
			throw new BSConfigException('設定内容が定義されていません。');
		}
		return $this->contents;
	}

	/**
	 * 変換前の設定内容を設定する
	 *
	 * @access public
	 * @param string $contents 設定内容
	 */
	public function setContents ($contents) {
		$this->contents = $contents;
		$this->result = null;
	}

	/**
	 * 変換後の設定内容を返す
	 *
	 * @access public
	 * @return mixed[] 設定内容
	 */
	public function getResult () {
		if (!$this->result) {
			// 設定内容を一時ファイルに書き出す
			$name = sprintf(
				'%s.%s.ini', 
				get_class($this),
				BSCrypt::getSHA1($this->getContents())
			);
			$file = new BSFile(BS_VAR_DIR . '/tmp/' . $name); // BSDirectoryFinderは使わない
			$file->setContents($this->getContents());

			$this->result = parse_ini_file($file->getPath(), true);
			$file->delete();
		}
		return $this->result;
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
		return BSMediaType::getType('ini');
	}

	/**
	 * エンコードを返す
	 *
	 * @access public
	 * @return string PHPのエンコード名
	 */
	public function getEncoding () {
		return BSString::SCRIPT_ENCODING;
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		return ($this->getResult() != null);
	}

	/**
	 * エラーメッセージを返す
	 *
	 * @access public
	 * @return string エラーメッセージ
	 */
	public function getError () {
		return '要素が含まれていません。';
	}
}

/* vim:set tabstop=4 ai: */
?>