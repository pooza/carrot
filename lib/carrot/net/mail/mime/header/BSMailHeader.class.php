<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mail.mime.header
 */

/**
 * 基底メールヘッダ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
class BSMailHeader {
	protected $part;
	protected $name;
	protected $contents;

	/**
	 * @access public
	 * @param BSMIMEPart $part メールパート
	 * @param string $name ヘッダ名
	 */
	public function __construct (BSMIMEPart $part, $name) {
		$this->part = $part;
		$this->name = $name;
	}

	/**
	 * 名前を返す
	 *
	 * @access public
	 * @return string ヘッダ名
	 */
	public function getName () {
		return $this->name;
	}

	/**
	 * 実体を返す
	 *
	 * @access public
	 * @return mixed 実体
	 */
	public function getEntity () {
		return $this->getContents();
	}

	/**
	 * 内容を返す
	 *
	 * @access public
	 * @return string 内容
	 */
	public function getContents () {
		return $this->contents;
	}

	/**
	 * 内容を設定
	 *
	 * @access public
	 * @param mixed $contents 内容
	 */
	public function setContents ($contents) {
		$this->contents = $contents;
	}

	/**
	 * 内容を追加
	 *
	 * @access public
	 * @param string $contents 内容
	 */
	public function appendContents ($contents) {
		$this->contents .= BSMIMEPart::LINE_SEPARATOR . $contents;
	}

	/**
	 * ヘッダを整形して返す
	 *
	 * @access public
	 * @param ヘッダ行
	 */
	public function format () {
		if (!$this->isVisible()) {
			return null;
		}

		$contents = BSMIMEUtility::encode($this->getContents());
		$contents = str_replace(
			BSMIMEUtility::ENCODE_PREFIX,
			"\n" . BSMIMEUtility::ENCODE_PREFIX,
			$contents
		);
		$contents = BSString::split($this->name . ': ' . $contents);

		$header = null;
		foreach (BSString::explode("\n", $contents) as $line) {
			if (!BSString::isBlank($header)) {
				$line = "\t" . $line;
			}
			$header .= $line . BSMIMEPart::LINE_SEPARATOR;
		}

		return $header;
	}

	/**
	 * 可視か？
	 *
	 * @access public
	 * @return boolean 可視ならばTrue
	 */
	public function isVisible () {
		return !BSString::isBlank($this->getContents());
	}

	/**
	 * 複数行を許容するか？
	 *
	 * @access public
	 * @return boolean 許容するならばTrue
	 */
	public function isMultiLine () {
		return false;
	}
}

/* vim:set tabstop=4: */
