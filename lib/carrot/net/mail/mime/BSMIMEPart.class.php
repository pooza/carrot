<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mail.mime
 */

/**
 * メールの各パート
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSMIMEPart {
	private $headers;
	private $renderer;
	private $filename;
	const ATTACHMENT = 'attachment';
	const INLINE = 'inline';
	const LINE_SEPARATOR = "\r\n";

	/**
	 * ヘッダを返す
	 *
	 * @access public
	 * @param string $name 名前
	 * @return string ヘッダ
	 */
	public function getHeader ($name) {
		$name = BSString::capitalize($name);
		return $this->getHeaders()->getParameter($name);
	}

	/**
	 * ヘッダを設定
	 *
	 * @access public
	 * @param string $name 名前
	 * @param string $value 値
	 */
	public function setHeader ($name, $value) {
		$name = BSString::stripControlCharacters($name);
		$name = BSString::capitalize($name);
		$value = BSString::stripControlCharacters($value);
		$value = trim($value);

		if (BSString::isBlank($value)) {
			$this->getHeaders()->removeParameter($name);
		} else {
			$this->getHeaders()->setParameter($name, $value);
		}
	}

	/**
	 * ヘッダ一式を返す
	 *
	 * @access public
	 * @return string[] ヘッダ一式
	 */
	public function getHeaders () {
		if (!$this->headers) {
			$this->headers = new BSArray;
		}
		return $this->headers;
	}

	/**
	 * Content-Typeを返す
	 *
	 * @access public
	 * @return string Content-Type
	 */
	public function getType () {
		return $this->getHeader('Content-Type');
	}

	/**
	 * レンダラーを返す
	 *
	 * @access public
	 * @return BSRenderer レンダラー
	 */
	public function getRenderer () {
		return $this->renderer;
	}

	/**
	 * レンダラーを設定
	 *
	 * @access public
	 * @param BSRenderer $renderer レンダラー
	 */
	public function setRenderer (BSRenderer $renderer) {
		$this->renderer = $renderer;
		$this->setHeader('Content-Type', BSMediaType::getFullContentType($renderer));
		$this->setHeader(
			'Content-Transfer-Encoding',
			BSMIMEUtility::getContentTransferEncoding($renderer)
		);
	}

	/**
	 * ファイル名を返す
	 *
	 * @access public
	 * @return string ファイル名
	 */
	public function getFileName () {
		return $this->filename;
	}

	/**
	 * ファイル名を設定
	 *
	 * @access public
	 * @param string $filename ファイル名
	 * @param string $mode モード
	 */
	public function setFileName ($filename, $mode = self::ATTACHMENT) {
		$this->filename = $filename;

		if (BSString::isBlank($filename)) {
			$this->getHeaders()->removeParameter('Content-Disposition');
		} else {
			$this->getHeaders()->setParameter(
				'Content-Disposition',
				sprintf('%s; filename="%s"', $mode, $filename)
			);
		}
	}

	/**
	 * ヘッダをMIMEエンコードして返す
	 *
	 * @access protected
	 * @param string $key フィールド名
	 * @param string $value フィールド値
	 * @param ヘッダ行
	 */
	protected function encodeHeader ($key, $value) {
		$value = BSMIMEUtility::encode($value);
		$value = str_replace(
			BSMIMEUtility::ENCODE_PREFIX,
			"\n" . BSMIMEUtility::ENCODE_PREFIX,
			$value
		);
		$body = BSString::split($key . ': ' . $value);

		$header = null;
		foreach (BSString::explode("\n", $body) as $line) {
			if (!BSString::isBlank($header)) {
				$line = "\t" . $line;
			}
			$header .= $line . self::LINE_SEPARATOR;
		}
		return $header;
	}
}

/* vim:set tabstop=4: */
