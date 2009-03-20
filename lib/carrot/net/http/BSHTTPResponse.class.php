<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http
 */

/**
 * httpレスポンス
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSHTTPResponse extends BSMIMEDocument {
	private $version;
	private $status;
	private $message;
	private $url;

	/**
	 * ヘッダ部をパース
	 *
	 * @access protected
	 * @param string $headers ヘッダ部
	 */
	protected function parseHeaders ($headers) {
		$this->getHeaders()->clearParameters();
		foreach (BSString::explode("\n", $headers) as $line) {
			if (preg_match('/^HTTP\/([0-9]+\.[0-9]+) ([0-9]{3}) (.*)$/', $line, $matches)) {
				$this->version = $matches[1];
				$this->status = (int)$matches[2];
				$this->message = $matches[3];
			} else if (preg_match('/^([a-z0-9\\-]+): *(.*)$/i', $line, $matches)) {
				$key = $matches[1];
				$this->setHeader($key, $matches[2]);
			} else if (preg_match('/^[\\t ]+(.*)$/', $line, $matches)) {
				$this->appendHeader($key, $matches[1]);
			}
		}
	}

	/**
	 * httpバージョンを返す
	 *
	 * @access public
	 * @return string httpバージョン
	 */
	public function getHTTPVersion () {
		return $this->version;
	}

	/**
	 * ステータスコードを返す
	 *
	 * @access public
	 * @return integer ステータスコード
	 */
	public function getStatus () {
		return $this->status;
	}

	/**
	 * リクエストされたURLを返す
	 *
	 * @access public
	 * @return BSURL リクエストされたURL
	 */
	public function getURL () {
		return $this->url;
	}

	/**
	 * リクエストされたURLを設定
	 *
	 * @access public
	 * @param BSURL $url リクエストされたURL
	 */
	public function setURL (BSURL $url) {
		$this->url = $url;
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		return ($this->getStatus() < 400);
	}

	/**
	 * エラーメッセージを返す
	 *
	 * @access public
	 * @return string エラーメッセージ
	 */
	public function getError () {
		if (!$this->validate()) {
			return $this->message;
		}
	}
}

/* vim:set tabstop=4: */
