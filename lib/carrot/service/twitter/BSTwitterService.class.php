<?php
/**
 * @package org.carrot-framework
 * @subpackage service.twitter
 */

/**
 * Twitterクライアント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSTwitterService extends BSCurlHTTP {
	private $oauth;
	const DEFAULT_HOST = 'twitter.com';
	const DEFAULT_HOST_MOBILE = 'mobile.twitter.com';

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
		$this->setSSL(true);
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
	public function sendGET ($path = '/') {
		if (!$this->oauth) {
			return parent::sendGET($path);
		}
		return $this->sendOauthRequest($this->createRequestURL($path), 'GET', new BSArray);
	}

	/**
	 * POSTリクエスト
	 *
	 * @access public
	 * @param string $path パス
	 * @param BSParameterHolder $params パラメータの配列
	 * @return BSHTTPResponse レスポンス
	 */
	public function sendPOST ($path = '/', BSParameterHolder $params = null) {
		$params = new BSArray($params);
		if (!$this->oauth) {
			return parent::sendPOST($path, $params);
		}
		return $this->sendOauthRequest($this->createRequestURL($path), 'POST', $params);
	}

	/**
	 * パスからリクエストURLを生成して返す
	 *
	 * @access protected
	 * @param string $href パス
	 * @return BSHTTPURL リクエストURL
	 */
	protected function createRequestURL ($href) {
		if (!BSString::isContain('.', $href)) {
			$href .= BS_SERVICE_TWITTER_SUFFIX;
		}
		return parent::createRequestURL($href);
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

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('Twitterサービス "%s"', $this->getName());
	}

	/**
	 * ツイートのURLを返す
	 *
	 * @access public
	 * @param string $id ツイートID
	 * @param string $account アカウント名
	 * @param BSUserAgent $useragent 対象ブラウザ
	 * @return BSHTTPURL URL
	 * @static
	 */
	static public function createTweetURL ($id, $account, BSUserAgent $useragent = null) {
		$url = BSURL::create();
		if (!$useragent) {
			$useragent = BSRequest::getInstance()->getUserAgent();
		}
		if ($useragent->isMobile()) {
			$url['host'] = self::DEFAULT_HOST_MOBILE;
			$url['path'] = '/' . $account . '/status/' . $id;
		} else {
			$url['host'] = self::DEFAULT_HOST;
			$url['path'] = '/#!/' . $account . '/status/' . $id;
		}
		return $url;
	}

	/**
	 * ツイートのURLをまとめて返す
	 *
	 * @access public
	 * @param string $id ツイートID
	 * @param string $account アカウント名
	 * @param string $prefix 要素名のプリフィックス
	 * @return BSArray URL文字列の配列
	 * @static
	 */
	static public function createTweetURLs ($id, $account, $prefix = 'url') {
		$urls = new BSArray;
		$useragents = new BSArray(array(
			null => BSUserAgent::create(BSUserAgent::DEFAULT_NAME),
			'_mobile' => BSUserAgent::create(BSDocomoUserAgent::DEFAULT_NAME),
		));
		foreach ($useragents as $suffix => $useragent) {
			$url = self::createTweetURL($id, $account, $useragent);
			$urls[$prefix . $suffix] = $url->getContents();
		}
		return $urls;
	}
}

/* vim:set tabstop=4: */
