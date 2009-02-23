<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mail.mime
 */

/**
 * 基底MIME文書
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSMIMEDocument implements BSRenderer {
	protected $headers;
	protected $contents;
	protected $body;
	protected $renderer;
	protected $filename;
	protected $boundary;
	protected $parts;
	const LINE_SEPARATOR = "\r\n";

	/**
	 * ヘッダを返す
	 *
	 * @access public
	 * @param string $name 名前
	 * @return BSMailHeader ヘッダ
	 */
	public function getHeader ($name) {
		$name = BSString::stripControlCharacters($name);
		$name = BSString::capitalize($name);
		return $this->getHeaders()->getParameter(strtolower($name));
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
		$header->setContents($value);
		$this->getHeaders()->setParameter(strtolower($header->getName()), $header);
		$this->contents = null;
	}

	/**
	 * ヘッダに追記
	 *
	 * @access public
	 * @param string $name 名前
	 * @param string $value 値
	 */
	public function appendHeader ($name, $value) {
		$name = BSString::stripControlCharacters($name);
		$name = BSString::capitalize($name);
		if (!is_array($value) && !is_object($value)) {
			$value = BSString::stripControlCharacters($value);
		}

		if ($this->getHeaders()->hasParameter(strtolower($name))) {
			$this->getHeader($name)->appendContents($value);
		} else {
			$this->setHeader($name, $header);
		}
		$this->contents = null;
	}

	/**
	 * ヘッダを削除
	 *
	 * @access public
	 * @param string $name 名前
	 */
	public function removeHeader ($name) {
		$this->getHeaders()->removeParameter($name);
		$this->contents = null;
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
	public function setFileName ($filename, $mode = BSMIMEUtility::ATTACHMENT) {
		$this->filename = $filename;

		if (BSString::isBlank($filename)) {
			$this->getHeaders()->removeParameter('Content-Disposition');
		} else {
			$value = sprintf('%s; filename="%s"', $mode, $filename);
			$this->setHeader('Content-Disposition', $value);
		}
	}

	/**
	 * 全てのパートを返す
	 *
	 * @access public
	 * @return BSArray 全てのパート
	 */
	public function getParts () {
		if (!$this->parts) {
			$this->parts = new BSArray;
		}
		return $this->parts;
	}

	/**
	 * メインパートを返す
	 *
	 * @access public
	 * @return BSMIMEDocument メインパート
	 */
	public function getMainPart () {
		if (!$this->getParts()->getParameter(0)) {
			$part = new BSMIMEDocument;
			$part->setRenderer(new BSPlainTextRenderer);
			$this->getParts()->setParameter(0, $part);
		}
		return $this->getParts()->getParameter(0);
	}

	/**
	 * メインパートを設定
	 *
	 * @access public
	 * @param BSMIMEDocument $part メインパート
	 */
	public function setMainPart (BSMIMEDocument $part) {
		$this->getParts()->setParameter(0, $part);
	}

	/**
	 * マルチパートか？
	 *
	 * @access public
	 * @return boolean マルチパートならばTrue
	 */
	public function isMultiPart () {
		return (1 < $this->getParts()->count());
	}

	/**
	 * 出力内容を返す
	 *
	 * @access public
	 */
	public function getContents () {
		if (!$this->contents) {
			foreach ($this->getHeaders() as $header) {
				$this->contents .= $header->format();
			}
			$this->contents .= self::LINE_SEPARATOR;
			$this->contents .= $this->getBody();
		}
		return $this->contents;
	}

	/**
	 * 出力内容を設定
	 *
	 * @param string $contents 出力内容
	 * @access public
	 */
	public function setContents ($contents) {
		$this->contents = $contents;

		try {
			$contents = BSString::explode("\n\n", $contents);
			$this->parseHeaders($contents[0]);
			$contents->removeParameter(0);
			$contents = $contents->join("\n\n");
			$this->parseBody($contents);
		} catch (Exception $e) {
			throw new BSMIMEException('MIME文書がパースできません。');
		}
	}

	/**
	 * ヘッダ部をパース
	 *
	 * @access protected
	 * @param string $headers ヘッダ部
	 */
	protected function parseHeaders ($headers) {
		$this->getHeaders()->clearParameters();
		foreach (BSString::explode("\n", $headers) as $line) {
			if (preg_match('/^([a-z0-9\\-]+): *(.+)$/i', $line, $matches)) {
				$key = $matches[1];
				$this->setHeader($key, $matches[2]);
			} else if (preg_match('/^[\\t ]+(.*)$/', $line, $matches)) {
				$this->appendHeader($key, $matches[1]);
			}
		}
	}

	/**
	 * 本文をパース
	 *
	 * @access protected
	 * @param string $body 本文
	 */
	protected function parseBody ($body) {
		$this->parts = new BSArray;
		$this->body = null;

		if ($this->isMultiPart()) {
			foreach (BSString::explode($this->getBoundary(), $body) as $value) {
				if (BSString::isBlank($value) || ($value == '--')) {
					continue;
				}
				$part = new BSMIMEDocument;
				$part->setContents($value);
				$this->getParts()->setParameter(null, $part);
			}
		} else {
			$this->getMainPart()->getRenderer()->setContents($body);
		}
	}

	/**
	 * 本文を返す
	 *
	 * マルチパートの場合、素（mixed/multipart）の本文を返す。
	 *
	 * @access public
	 * @return string 本文
	 */
	public function getBody () {
		if (!$this->isMultiPart()) {
			return $this->getMainPart()->getRenderer()->getContents();
		}

		if (!$this->body) {
			foreach ($this->getParts() as $part) {
				$this->body .= '--' . $this->getBoundary() . self::LINE_SEPARATOR;
				foreach ($part->getHeaders() as $header) {
					$this->body .= $header->format();
				}
				$this->body .= self::LINE_SEPARATOR;
				$contents = $part->getRenderer()->getContents();
				if ($part->getHeader('Content-Transfer-Encoding')->getContents() == 'base64') {
					$contents = BSMIMEUtility::encodeBase64($contents, BSMIMEUtility::WITH_SPLIT);
				}
				$this->body .= $contents;
			}
			$this->body .= '--' . $this->getBoundary() . '--';
		}
		return $this->body;
	}

	/**
	 * 本文を設定
	 *
	 * マルチパートの場合でも、メインパートの本文を設定する。
	 *
	 * @access public
	 * @param string $body 本文
	 */
	public function setBody ($body) {
		$renderer = $this->getMainPart()->getRenderer();
		if (!method_exists($renderer, 'setContents')) {
			throw new BSMIMEException('%sの本文を上書きできません。', get_glass($renderer));
		}
		$renderer->setContents($body);
	}

	/**
	 * 添付ファイルを追加
	 *
	 * @access public
	 * @param BSRenderer $renderer レンダラー
	 * @param string $name ファイル名
	 * @return BSMIMEDocument 追加されたパート
	 */
	public function addAttachment (BSRenderer $renderer, $name = null) {
		$part = new BSMIMEDocument;
		$part->setRenderer($renderer);
		if (!BSString::isBlank($name)) {
			$part->setFileName($name, BSMIMEUtility::ATTACHMENT);
		}

		$parts = $this->getParts();
		$parts[] = $part;
		$this->body = null;
		$this->contents = null;

		if ($this->isMultiPart()) {
			$this->setHeader('Content-Type', 'multipart/mixed; boundary=' . $this->getBoundary());
			$this->setHeader('Content-Transfer-Encoding', null);
		} else {
			foreach (array('Content-Type', 'Content-Transfer-Encoding') as $name) {
				$this->setHeader($name, $part->getHeader($name)->getContents());
			}
		}

		return $part;
	}

	/**
	 * バウンダリを返す
	 *
	 * @access public
	 * @return string バウンダリ
	 */
	public function getBoundary () {
		if (!$this->boundary) {
			$this->boundary = BSUtility::getUniqueID();
		}
		return $this->boundary;
	}

	/**
	 * バウンダリを設定
	 *
	 * @access public
	 * @param string $boundary バウンダリ
	 */
	public function setBoundary ($boundary) {
		$this->boundary = $boundary;
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
		return BSMIMEType::getType('mime');
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		return true;
	}

	/**
	 * エラーメッセージを返す
	 *
	 * @access public
	 * @return string エラーメッセージ
	 */
	public function getError () {
		return null;
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('MIME文書 "%s"', $this->getMessageID());
	}
}

/* vim:set tabstop=4: */
