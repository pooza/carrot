<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage zipcode
 */

/**
 * 郵便番号
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSZipcode implements BSAssignable {
	private $contents;
	private $major;
	private $minor;
	const PATTERN = '^([[:digit:]]{3})-?([[:digit:]]{4})$';

	/**
	 * @access public
	 * @param string $value 内容
	 */
	public function __construct ($value) {
		if (!mb_ereg(self::PATTERN, $value, $matches)) {
			throw new BSZipcodeException($value . 'は正しい郵便番号ではありません。');
		}
		$this->major = $matches[1];
		$this->minor = $matches[2];
	}

	/**
	 * 郵便番号を返す
	 *
	 * @access public
	 * @return string 郵便番号
	 */
	public function getContents () {
		if (!$this->contents) {
			$this->contents = sprintf('%s-%s', $this->major, $this->minor);
		}
		return $this->contents;
	}

	/**
	 * アサインすべき値を返す
	 *
	 * @access public
	 * @return mixed アサインすべき値
	 */
	public function getAssignableValues () {
		return $this->getContents();
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('郵便番号 "%s"', $this->getContents());
	}
}

/* vim:set tabstop=4: */
