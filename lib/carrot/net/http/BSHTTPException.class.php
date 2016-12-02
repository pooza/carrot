<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net.http
 */

/**
 * HTTP例外
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSHTTPException extends BSNetException {
	private $response;

	/**
	 * 例外を含んだレスポンスを返す
	 *
	 * @return BSHTTPResponse レスポンス
	 */
	public function getResponse () {
		return $this->response;
	}

	/**
	 * レスポンスを格納
	 *
	 * @param BSHTTPResponse $response レスポンス
	 */
	public function setResponse (BSHTTPResponse $response) {
		$this->response = $response;
	}
}

/* vim:set tabstop=4: */
