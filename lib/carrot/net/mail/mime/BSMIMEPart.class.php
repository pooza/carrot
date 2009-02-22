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
	 * @return BSMailHeader ヘッダ
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
		if (!is_array($value) && !is_object($value)) {
			$value = BSString::stripControlCharacters($value);
		}

		try {
			$class = str_replace('-', '', $name);
			$class = BSClassLoader::getInstance()->getClassName($class, 'MailHeader');
		} catch (Exception $e) {
			$class = 'BSMailHeader';
		}
		$header = new $class($this, $name);

		if ($header->isMultiLine() && $this->getHeaders()->hasParameter($name)) {
			$this->getHeaders()->getParameter($name)->appendContents($value);
		} else {
			$header->setContents($value);
			$this->getHeaders()->setParameter($name, $header);
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
		return $this->getHeader('Content-Type')->getContents();
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
		$this->setHeader('Content-Type', $renderer);
		$this->setHeader('Content-Transfer-Encoding', $renderer);
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
			$value = sprintf('%s; filename="%s"', $mode, $filename);
			$this->setHeader('Content-Disposition', $value);
		}
	}
}

/* vim:set tabstop=4: */
