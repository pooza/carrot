<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mime
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
	 * @return BSMIMEHeader ヘッダ
	 */
	public function getHeader ($name) {
		$header = BSMIMEHeader::getInstance($name);
		$name = strtolower($header->getName());
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
		$header = BSMIMEHeader::getInstance($name);
		if ($header->isMultiple() && $this->getHeader($name)) {
			$header = $this->getHeader($name);
			$header->setContents($value);
		} else {
			$header->setPart($this);
			$header->setContents($value);
			$this->getHeaders()->setParameter(strtolower($header->getName()), $header);
		}
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
		if ($header = $this->getHeader($name)) {
			$header->appendContents($value);
			$this->contents = null;
		} else {
			$this->setHeader($name, $value);
		}
	}

	/**
	 * ヘッダを削除
	 *
	 * @access public
	 * @param string $name 名前
	 */
	public function removeHeader ($name) {
		if ($header = $this->getHeader($name)) {
			$this->getHeaders()->removeParameter(strtolower($header->getName()));
			$this->contents = null;
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
	 * メッセージIDを返す
	 *
	 * @access public
	 * @return string メッセージID
	 */
	public function getMessageID () {
		if ($header = $this->getHeader('Message-Id')) {
			return $header->getEntity();
		}
	}

	/**
	 * Content-Transfer-Encodingを返す
	 *
	 * @access public
	 * @return string Content-Transfer-Encoding
	 */
	public function getContentTransferEncoding () {
		if ($header = $this->getHeader('Content-Transfer-Encoding')) {
			return $header->getContents();
		}
	}

	/**
	 * レンダラーを返す
	 *
	 * @access public
	 * @return BSRenderer レンダラー
	 */
	public function getRenderer () {
		if (!$this->renderer) {
			$this->setRenderer(new BSRawRenderer);
		}
		return $this->renderer;
	}

	/**
	 * レンダラーを設定
	 *
	 * @access public
	 * @param BSRenderer $renderer レンダラー
	 * @param integer $flag フラグ
	 *   BSMIMEUtility::WITHOUT_HEADER ヘッダを修正しない
	 *   BSMIMEUtility::WITH_HEADER ヘッダも修正
	 */
	public function setRenderer (BSRenderer $renderer, $flag = BSMIMEUtility::WITH_HEADER) {
		$this->renderer = $renderer;
		if ($flag & BSMIMEUtility::WITH_HEADER) {
			$this->setHeader('Content-Type', $renderer);
			$this->setHeader('Content-Transfer-Encoding', $renderer);
		}
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
		if (BSString::isBlank($filename)) {
			$this->removeHeader('Content-Disposition');
		} else {
			$this->filename = $filename;
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
	 * マルチパートか？
	 *
	 * @access public
	 * @return boolean マルチパートならばTrue
	 */
	public function isMultiPart () {
		if (0 < $this->getParts()->count()) {
			return true;
		} else {
			if ($header = $this->getHeader('Content-Type')) {
				if (preg_match('/^multipart\//', $header->getContents())) {
					return true;
				}
			}
		}
		return false;
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
	 * 出力内容をクリア
	 *
	 * @access public
	 */
	public function clearContents () {
		$this->contents = null;
		$this->body = null;
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
			if (preg_match('/^([a-z0-9\\-]+): *(.*)$/i', $line, $matches)) {
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
		if ($this->isMultiPart()) {
			$separator = '--' . $this->getBoundary();
			$parts = BSString::explode($separator, $body);
			$parts->removeParameter($parts->count() - 1);
			$parts->removeParameter(0);
			foreach ($parts as $source) {
				$part = new BSMIMEDocument;
				$part->setContents($source);
				$this->getParts()->setParameter(null, $part);
			}
		} else {
			if ($header = $this->getHeader('Content-Type')) {
				if ($header['main_type'] == 'text') {
					$renderer = new BSPlainTextRenderer;
					$renderer->setLineSeparator(self::LINE_SEPARATOR);
					if ($encoding = $header['charset']) {
						$renderer->setEncoding($encoding);
						$body = BSString::convertEncoding($body, 'utf-8', $encoding);
					}
					$this->setRenderer($renderer, BSMIMEUtility::WITHOUT_HEADER);
				}
			}
			$this->getRenderer()->setContents($body);
		}
	}

	/**
	 * 本文を返す
	 *
	 * マルチパートの場合、素（multipart/mixed）の本文を返す。
	 *
	 * @access public
	 * @return string 本文
	 */
	public function getBody () {
		if (!$this->body) {
			if ($renderer = $this->getRenderer()) {
				$body = $renderer->getContents();
				if ($this->getContentTransferEncoding() == 'base64') {
					$body = BSMIMEUtility::encodeBase64($body, BSMIMEUtility::WITH_SPLIT);
				}
				$this->body .= $body;
			}
			if ($this->isMultiPart()) {
				foreach ($this->getParts() as $part) {
					$this->body .= '--' . $this->getBoundary() . self::LINE_SEPARATOR;
					$this->body .= $part->getContents();
				}
				$this->body .= '--' . $this->getBoundary() . '--';
			}
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
		if (!method_exists($this->getRenderer(), 'setContents')) {
			throw new BSMIMEException('%sの本文を上書きできません。', get_glass($renderer));
		}
		$this->getRenderer()->setContents($body);
	}

	/**
	 * 本文や添付ファイルの実体を返す
	 *
	 * マルチパートメールの場合は配列
	 *
	 * @access public
	 * @return mixed 
	 */
	public function getEntities () {
		if ($this->isMultiPart()) {
			$parts = new BSArray;
			foreach ($this->getParts() as $part) {
				$name = null;
				if ($header = $part->getHeader('Content-Disposition')) {
					$name = $header['filename'];
				}
				$parts[$name] = $part->getEntities();
			}
			return $parts;
		} else {
			if ($header = $this->getHeader('Content-Type')) {
				$entity = $this->getRenderer()->getContents();
				switch ($this->getContentTransferEncoding()) {
					case 'base64':
						$entity = BSMIMEUtility::decodeBase64($entity);
						break;
					case 'quoted-printable':
						$entity = BSMIMEUtility::decodeQuotedPrintable($entity);
						break;
				}
				if ($header['main_type'] == 'text') {
					$entity = BSString::convertEncoding($entity, 'utf-8', $header['charset']);
				}
				return $entity;
			}
			return $this->getRenderer();
		}
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
			$this->setHeader('Content-Type', $renderer);
			$this->setHeader('Content-Transfer-Encoding', $renderer);
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
