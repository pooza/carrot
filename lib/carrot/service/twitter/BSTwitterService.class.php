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
class BSTwitterService extends BSCurlHTTP implements BSSerializable {
	protected $requestToken;
	protected $accessToken;
	private $oauth;
	private $account;
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

		$this->requestToken = new BSArray;
		$this->accessToken = new BSArray;
		if ($tokens = $this->getSerialized()) {
			foreach ($tokens as $key => $values) {
				$this->$key->setParameters($values);;
			}
		}
	}

	/**
	 * @access public
	 */
	public function __destruct () {
		$this->serialize();
	}

	/**
	 * OAuth認証ページのURLを返す
	 *
	 * @access public
	 * @return BSHTTPURL 認証ページのURL
	 */
	public function getOAuthURL () {
		BSUtility::includeFile('twitteroauth/twitteroauth.php');
		$oauth = new TwitterOAuth(
			BS_SERVICE_TWITTER_CONSUMER_KEY,
			BS_SERVICE_TWITTER_CONSUMER_SECRET
		);
		$this->requestToken = new BSArray($oauth->getRequestToken());
		$this->accessToken = null;

		return BSURL::getInstance($oauth->getAuthorizeURL($this->requestToken['oauth_token']));
	}

	/**
	 * OAuth認証
	 *
	 * @access public
	 * @param string $verifier 認証ページが返したトークン
	 */
	public function login ($verifier) {
		if (!$this->requestToken) {
			return false;
		}

		BSUtility::includeFile('twitteroauth/twitteroauth.php');
		$oauth = new TwitterOAuth(
			BS_SERVICE_TWITTER_CONSUMER_KEY,
			BS_SERVICE_TWITTER_CONSUMER_SECRET,
			$this->requestToken['oauth_token'],
			$this->requestToken['oauth_token_secret']
		);
		$this->accessToken = new BSArray($oauth->getAccessToken($verifier));
	}

	/**
	 * ログイン中のアカウントを返す
	 *
	 * @access public
	 * @return BSTwitterAccount アカウント
	 */
	public function getAccount () {
		if (!$this->account && ($name = $this->accessToken['screen_name'])) {
			$this->account = new BSTwitterAccount($name);
		}
		return $this->account;
	}

	/**
	 * GETリクエスト
	 *
	 * @access public
	 * @param string $path パス
	 * @return BSHTTPResponse レスポンス
	 */
	public function sendGetRequest ($path = '/') {
		if (BSString::isBlank($this->accessToken['oauth_token'])) {
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
		if (BSString::isBlank($this->accessToken['oauth_token'])) {
			return parent::sendPostRequest($path);
		}

		if ($params) {
			$params = new BSArray;
		}
		$url = BSURL::getInstance('https://' . self::DEFAULT_HOST);
		$url['path'] = $path;
		return $this->sendOauthRequest($url, 'POST', $params);
	}

	private function sendOauthRequest (BSHTTPURL $url, $method, BSArray $params) {
		$contents = $this->getOAuth()->OAuthRequest(
			$url->getContents(),
			$method,
			$params->getParameters()
		);

		$response = new BSHTTPResponse;
		$response->getRenderer()->setContents($contents);
		foreach ($this->getOAuth()->http_header as $key => $value) {
			$key = str_replace('_', '-', $key);
			$response->setHeader($key, $value);
		}
		return $response;
	}

	private function getOAuth () {
		if (!$this->oauth) {
			if (BSString::isBlank($this->accessToken['oauth_token'])) {
				throw new BSTwitterException('OAuth認証が行われていません。');
			}

			BSUtility::includeFile('twitteroauth/twitteroauth.php');
			$this->oauth = new TwitterOAuth(
				BS_SERVICE_TWITTER_CONSUMER_KEY,
				BS_SERVICE_TWITTER_CONSUMER_SECRET,
				$this->accessToken['oauth_token'],
				$this->accessToken['oauth_token_secret']
			);
		}
		return $this->oauth;
	}

	/**
	 * 属性名へシリアライズ
	 *
	 * @access public
	 * @return string 属性名
	 */
	public function serializeName () {
		return get_class($this);
	}

	/**
	 * シリアライズ
	 *
	 * @access public
	 */
	public function serialize () {
		$values = array();
		foreach (array('requestToken', 'accessToken') as $key) {
			$values[$key] = $this->$key;
		}
		BSController::getInstance()->setAttribute($this, $values);
	}

	/**
	 * シリアライズ時の値を返す
	 *
	 * @access public
	 * @return mixed シリアライズ時の値
	 */
	public function getSerialized () {
		return BSController::getInstance()->getAttribute($this);
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('Twitterサービス "%s"', $this->getName());
	}
}

/* vim:set tabstop=4: */
