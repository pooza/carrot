<?php
/**
 * @package org.carrot-framework
 * @subpackage service.twitter
 */

/**
 * Twitterクライアント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSTwitterService extends BSCurlHTTP {
	private $oauth;
	const DEFAULT_HOST = 'twitter.com';

	/**
	 * @access public
	 * @param BSHost $host ホスト
	 * @param integer $port ポート
	 */
	public function __construct (BSHost $host = null, $port = null) {
		if (!$host) {
			$host = new BSHost(self::DEFAULT_HOST);
		}
		parent::__construct($host, $port);
	}

	/**
	 * OAuthオブジェクトを返す
	 *
	 * @access public
	 * @return TwitterOAuth
	 */
	public function getOAuth () {
		return $this->oauth;
	}

	/**
	 * OAuthオブジェクトを設定
	 *
	 * @access public
	 * @param TwitterOAuth $oauth
	 */
	public function setOAuth (TwitterOAuth $oauth) {
		$this->oauth = $oauth;
	}

	/**
	 * GETリクエスト
	 *
	 * @access public
	 * @param string $path パス
	 * @return BSHTTPResponse レスポンス
	 */
	public function sendGetRequest ($path = '/') {
		if (!$this->oauth) {
			return parent::sendGetRequest($path);
		}

		$url = BSURL::getInstance('https://' . self::DEFAULT_HOST);
		$url['path'] = $path;
		return $this->sendOauthRequest($url, 'GET', new BSArray);
	}

	/**
	 * POSTリクエスト
	 *
	 * @access public
	 * @param string $path パス
	 * @param string[] $params パラメータの配列
	 * @return BSHTTPResponse レスポンス
	 */
	public function sendPostRequest ($path = '/', $params = null) {
		if ($params) {
			$params = new BSArray;
		}

		if (!$this->oauth) {
			return parent::sendPostRequest($path, $params);
		}

		$url = BSURL::getInstance('https://' . self::DEFAULT_HOST);
		$url['path'] = $path;
		return $this->sendOauthRequest($url, 'POST', $params);
	}

	private function sendOauthRequest (BSHTTPURL $url, $method, BSArray $params) {
		$contents = $this->oauth->OAuthRequest(
			$url->getContents(),
			$method,
			$params->getParameters()
		);

		$response = new BSHTTPResponse;
		$response->setStatus($this->oauth->http_code);
		$response->getRenderer()->setContents($contents);
		foreach ($this->oauth->http_header as $key => $value) {
			$key = str_replace('_', '-', $key);
			$response->setHeader($key, $value);
		}
		return $response;
	}
}

/* vim:set tabstop=4: */
