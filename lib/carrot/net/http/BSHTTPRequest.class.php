<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http
 */

/**
 * httpリクエスト
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSHTTPRequest extends BSMIMEDocument {
	private $version = '1.0';
	private $method;
	private $path;

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
	 * メソッドを返す
	 *
	 * @access public
	 * @return string メソッド
	 */
	public function getMethod () {
		return $this->method;
	}

	/**
	 * メソッドを設定
	 *
	 * @access public
	 * @param string $method メソッド
	 */
	public function setMethod ($method) {
		$this->method = $method;
	}

	/**
	 * 送信先パスを返す
	 *
	 * @access public
	 * @return string 送信先パス
	 */
	public function getPath () {
		return $this->path;
	}

	/**
	 * 送信先パスを設定
	 *
	 * @access public
	 * @param string $path 送信先パス
	 */
	public function setPath ($path) {
		$this->path = $path;
	}

	/**
	 * 出力内容を返す
	 *
	 * @access public
	 */
	public function getContents () {
		$this->setHeader('Content-Length', $this->getRenderer()->getSize());
		return sprintf(
			'%s %s HTTP/%s',
			$this->getMethod(),
			$this->getPath(),
			$this->getHTTPVersion()
		) . BSHTTP::LINE_SEPARATOR . parent::getContents();
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		return $this->getMethod() && $this->getPath();
	}

	/**
	 * エラーメッセージを返す
	 *
	 * @access public
	 * @return string エラーメッセージ
	 */
	public function getError () {
		return 'メソッド又はパスが空欄です。';
	}
}

/* vim:set tabstop=4: */
