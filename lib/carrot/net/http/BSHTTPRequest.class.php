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
	private $url;

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
	 * 送信先URLを返す
	 *
	 * @access public
	 * @return BSURL 送信先URL
	 */
	public function getURL () {
		return $this->url;
	}

	/**
	 * 送信先URLを設定
	 *
	 * @access public
	 * @param BSURL $url 送信先URL
	 */
	public function setURL (BSURL $url) {
		$this->url = $url;
		$this->setHeader('Host', $url['host']);
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
			$this->getURL()->getFullPath(),
			$this->getVersion()
		) . self::LINE_SEPARATOR . parent::getContents();
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		return $this->getMethod() && $this->getURL();
	}

	/**
	 * エラーメッセージを返す
	 *
	 * @access public
	 * @return string エラーメッセージ
	 */
	public function getError () {
		return 'メソッド又は送信先URLが空欄です。';
	}
}

/* vim:set tabstop=4: */
