<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mail.mime
 */

/**
 * MIME文書
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSMIMEDocument extends BSMIMEPart implements BSRenderer {
	private $body;
	private $parts;
	private $contents;

	/**
	 * ヘッダを設定
	 *
	 * @access public
	 * @param string $name 名前
	 * @param string $value 値
	 */
	public function setHeader ($name, $value) {
		parent::setHeader($name, $value);
		$this->contents = null;
	}

	/**
	 * レンダラーを返す
	 *
	 * @access public
	 * @return BSRenderer レンダラー
	 */
	public function getRenderer () {
		throw new BSMIMEException('%s::getRendererは利用できません。', get_class($this));
	}

	/**
	 * レンダラーを設定
	 *
	 * @access public
	 * @param BSRenderer $renderer レンダラー
	 */
	public function setRenderer (BSRenderer $renderer) {
		throw new BSMIMEException('%s::setRendererは利用できません。', get_class($this));
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
	 * @return BSMIMEPart メインパート
	 */
	public function getMainPart () {
		return $this->getParts()->getParameter(0);
	}

	/**
	 * メインパートを設定
	 *
	 * @access public
	 * @param BSMIMEPart $part メインパート
	 */
	public function setMainPart (BSMIMEPart $part) {
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
	}

	/**
	 * 本文を返す
	 *
	 * @access public
	 * @param string $body 本文
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
