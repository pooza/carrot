<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net.http
 */

/**
 * httpレスポンス
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSHTTPResponse extends BSMIMEDocument {
	protected $version;
	protected $status;
	protected $message;
	protected $url;
	const STATUS_PATTERN = '^HTTP/([[:digit:]]+\\.[[:digit:]]+) ([[:digit:]]{3}) (.*)$';

	/**
	 * ヘッダ部をパース
	 *
	 * @access protected
	 * @param string $headers ヘッダ部
	 */
	protected function parseHeaders ($headers) {
		$this->getHeaders()->clear();
		$headers = BSString::convertLineSeparator($headers);
		foreach (BSString::explode("\n", $headers) as $line) {
			if (mb_ereg(self::STATUS_PATTERN, $line, $matches)) {
				$this->version = $matches[1];
				$this->status = (int)$matches[2];
				$this->message = $matches[3];
			} else if (mb_ereg('^([-[:alnum:]]+): *(.*)$', $line, $matches)) {
				$key = $matches[1];
				$this->setHeader($key, $matches[2]);
			} else if (mb_ereg('^[[:blank:]]+(.*)$', $line, $matches)) {
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
	public function getVersion () {
		return $this->version;
	}

	/**
	 * ステータスコードを返す
	 *
	 * @access public
	 * @return integer ステータスコード
	 */
	public function getStatus () {
		if ($header = $this->getHeader('status')) {
			return $header['code'];
		} else {
			return $this->status;
		}
	}

	/**
	 * ステータスコードを設定
	 *
	 * @access public
	 * @param integer $code ステータスコード
	 */
	public function setStatus ($code) {
		$this->status = $code;
		$this->setHeader('status', $code);
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
	 * @param BSHTTPRedirector $url リクエストされたURL
	 */
	public function setURL (BSHTTPRedirector $url) {
		$this->url = $url->createURL();
	}

	/**
	 * HTML文書か？
	 *
	 * @access public
	 * @return boolean HTML文書ならTrue
	 */
	public function isHTML () {
		return ($header = $this->getHeader('Content-Type')) && $header->isHTML();
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
