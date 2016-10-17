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
	protected $consumerKey;
	protected $consumerSecret;
	protected $accessToken;
	protected $accessTokenSecret;
	protected $credential;
	protected $bearerToken;
	protected $signatureKey;
	protected $oauth;
	protected $serializeHandler;
	const DEFAULT_HOST = 'api.twitter.com';

	/**
	 * @access public
	 * @param BSHost $host ホスト
	 * @param integer $port ポート
	 */
	public function __construct (BSHost $host = null, $port = null) {
		if (!$host) {
			$host = new BSHost(self::DEFAULT_HOST);
			$port = BSNetworkService::getPort('https');
		}
		parent::__construct($host, $port);
	}

	/**
	 * コンシューマキーを返す
	 *
	 * @access public
	 * @return string コンシューマキー
	 */
	public function getConsumerKey () {
		if (!$this->consumerKey) {
			$this->consumerKey = BS_SERVICE_TWITTER_CONSUMER_KEY;
			$this->credential = null;
		}
		return $this->consumerKey;
	}

	/**
	 * コンシューマキーを設定
	 *
	 * @access public
	 * @param string $value コンシューマキー
	 */
	public function setConsumerKey ($value) {
		$this->consumerKey = $value;
		$this->credential = null;
	}

	/**
	 * コンシューマシークレットを返す
	 *
	 * @access public
	 * @return string コンシューマシークレット
	 */
	public function getConsumerSecret () {
		if (!$this->consumerSecret) {
			$this->consumerSecret = BS_SERVICE_TWITTER_CONSUMER_SECRET;
			$this->credential = null;
			$this->consumerKey = null;
		}
		return $this->consumerSecret;
	}

	/**
	 * コンシューマシークレットを設定
	 *
	 * @access public
	 * @param string $value コンシューマシークレット
	 */
	public function setConsumerSecret ($value) {
		$this->consumerSecret = $value;
		$this->credential = null;
		$this->consumerKey = null;
	}

	/**
	 * アクセストークンを返す
	 *
	 * @access public
	 * @return string アクセストークン
	 */
	public function getAccessToken () {
		if (!$this->accessToken) {
			$this->accessToken = BS_SERVICE_TWITTER_ACCESS_TOKEN;
		}
		return $this->accessToken;
	}

	/**
	 * アクセストークンを設定
	 *
	 * @access public
	 * @param string $value アクセストークン
	 */
	public function setAccessToken ($value) {
		$this->accessToken = $value;
	}

	/**
	 * アクセストークンシークレットを返す
	 *
	 * @access public
	 * @return string アクセストークンシークレット
	 */
	public function getAccessTokenSecret () {
		if (!$this->accessTokenSecret) {
			$this->accessTokenSecret = BS_SERVICE_TWITTER_ACCESS_TOKEN_SECRET;
			$this->consumerKey = null;
		}
		return $this->accessTokenSecret;
	}

	/**
	 * アクセストークンシークレットを設定
	 *
	 * @access public
	 * @param string $value アクセストークンシークレット
	 */
	public function setAccessTokenSecret ($value) {
		$this->accessTokenSecret = $value;
		$this->consumerKey = null;
	}

	/**
	 * クレデンシャルを返す
	 *
	 * @access public
	 * @return string クレデンシャル
	 */
	public function getCredential () {
		if (!$this->credential) {
			$this->credential = BSMIMEUtility::encodeBase64(
				$this->getConsumerKey() . ':' . $this->getConsumerSecret()
			);
		}
		return $this->credential;
	}

	/**
	 * ベアラートークンを更新
	 *
	 * @access public
	 */
	public function updateBearerToken () {
		$key = (new BSArray([
			$this->getConsumerKey(),
			$this->getConsumerSecret(),
			__CLASS__,
			__FUNCTION__,
		]))->join(':');
		$date = BSDate::getNow();
		$date['minute'] = '-' . BS_SERVICE_TWITTER_MINUTES;
		if (!$value = $this->getSerializeHandler()->getAttribute($key, $date)) {
			$request = new BSHTTPRequest;
			$request->setMethod('POST');
			$request->setURL($this->createRequestURL('/oauth2/token'));
			$request->setHeader('Authorization', 'Basic ' . $this->getCredential());
			$request->setRenderer(new BSWWWFormRenderer);
			$request->getRenderer()->setParameter('grant_type', 'client_credentials');
			$this->setAttribute('post', true);
			$this->setAttribute('postfields', $request->getRenderer()->getContents());
			$response = $this->send($request);
			$this->log($response);
	
			$json = new BSJSONRenderer;
			$json->setContents($response->getRenderer()->getContents());
			$value = $json->getResult()['access_token'];
			$this->getSerializeHandler()->setAttribute($key, $value);
		}
		$this->bearerToken = $value;
	}

	protected function getSignatureKey () {
		if (!$this->signatureKey) {
			$key = new BSStringFormat('%s&%s');
			$key[] = BSURL::encode($this->getConsumerSecret());
			$key[] = BSURL::encode($this->getAccessTokenSecret());
			$this->signatureKey = $key->getContents();
		}
		return $this->signatureKey;
	}

	/**
	 * OAuthのパラメータを設定
	 *
	 * @access public
	 * @param BSHTTPRedirector $url エンドポイントのURL
	 * @param BSParameterHolder $params パラメータ
	 * @return BSWWWFormRenderer OAuthパラメータ
	 */
	public function setOAuth (BSHTTPRedirector $url, BSParameterHolder $params) {
		$params = new BSArray($params);
		$params['oauth_token'] = $this->getAccessToken();
		$params['oauth_consumer_key'] = $this->getConsumerKey();
		$params['oauth_signature_method'] = 'HMAC-SHA1';
		$params['oauth_timestamp'] = BSDate::getNow()->getTimestamp();
		$params['oauth_nonce'] = BSDate::getNow('YmdHis') . BSNumeric::getRandom(1000, 9999);
		$params['oauth_version'] = '1.0';
		$params->sort();
		$this->oauth = new BSWWWFormRenderer;
		$this->oauth->setParameters($params);

		$signature = new BSArray([
			'POST',
			BSURL::encode($url->getContents()),
			BSURL::encode(str_replace(
				['+' , '%7E'],
				['%20' , '~'],
				$this->oauth->getContents()
			)),
		]);

		$this->oauth['oauth_signature'] = base64_encode(hash_hmac(
			'sha1',
			$signature->join('&'),
			$this->getSignatureKey(),
			true
		));
	}

	/**
	 * タイムラインを返す
	 *
	 * @access public
	 * @param string $account アカウント
	 * @param integer $count ツイート数
	 * @return BSArray タイムライン
	 */
	public function getTimeline ($account, $count = 10) {
		$key = (new BSArray([
			$account,
			$count,
			__CLASS__,
			__FUNCTION__,
		]))->join(':');
		$date = BSDate::getNow();
		$date['minute'] = '-' . BS_SERVICE_TWITTER_MINUTES;
		if (!$timeline = $this->getSerializeHandler()->getAttribute($key, $date)) {
			$timeline = new BSArray;
			$url = $this->createRequestURL('/1.1/statuses/user_timeline.json');
			$url->setParameter('screen_name', $account);
			$url->setParameter('count', $count);
			$response = $this->sendGET($url->getFullPath());
	
			$json = new BSJSONRenderer;
			$json->setContents($response->getRenderer()->getContents());
			foreach ($json->getResult() as $entry) {
				$urls = self::createTweetURLs($entry['id_str'], $account);
				$timeline[] = new BSArray([
					'id' => $entry['id_str'],
					'from_user' => $entry['user']['screen_name'],
					'text' => $entry['text'],
					'created_at' => $entry['created_at'],
					'url0' => $urls['url0'],
					'url_mobile' => $urls['url_mobile'],
					'profile_image_url' => $entry['user']['profile_image_url_https'],
				]);
			}
			$this->getSerializeHandler()->setAttribute($key, $timeline);
		}
		return $timeline;
	}

	/**
	 * ツイートを検索して返す
	 *
	 * @access public
	 * @param string $keyword キーワード
	 * @param integer $count ツイート数
	 * @return BSArray ツイート
	 */
	public function searchTweets ($keyword, $count = 10) {
		$key = (new BSArray([
			$keyword,
			$count,
			__CLASS__,
			__FUNCTION__,
		]))->join(':');
		$date = BSDate::getNow();
		$date['minute'] = '-' . BS_SERVICE_TWITTER_MINUTES;
		if (!$timeline = $this->getSerializeHandler()->getAttribute($key, $date)) {
			$timeline = new BSArray;
			$url = $this->createRequestURL('/1.1/search/tweets.json');
			$url->setParameter('q', $keyword);
			$url->setParameter('count', $count);
			$response = $this->sendGET($url->getFullPath());

			$json = new BSJSONRenderer;
			$json->setContents($response->getRenderer()->getContents());
			foreach ($json->getResult()['statuses'] as $entry) {
				$urls = self::createTweetURLs($entry['id'], $entry['user']['screen_name']);
				$timeline[] = new BSArray([
					'id' => $entry['id'],
					'from_user' => $entry['user']['screen_name'],
					'text' => $entry['text'],
					'created_at' => $entry['created_at'],
					'url0' => $urls['url0'],
					'url_mobile' => $urls['url_mobile'],
					'profile_image_url' => $entry['user']['profile_image_url_https'],
				]);
			}
			$this->getSerializeHandler()->setAttribute($key, $timeline);
		}
		return $timeline;
	}

	/**
	 * プロフィールを返す
	 *
	 * @access public
	 * @param string $account アカウント
	 * @return BSArray プロフィール
	 */
	public function getProfile ($account) {
		$key = (new BSArray([
			$account,
			__CLASS__,
			__FUNCTION__,
		]))->join(':');
		$date = BSDate::getNow();
		$date['minute'] = '-' . BS_SERVICE_TWITTER_MINUTES;
		if (!$profile = $this->getSerializeHandler()->getAttribute($key, $date)) {
			$url = $this->createRequestURL('/1.1/users/show.json');
			$url->setParameter('screen_name', $account);
			$response = $this->sendGET($url->getFullPath());
			$json = new BSJSONRenderer;
			$json->setContents($response->getRenderer()->getContents());
			$profile = $json->getResult();
			$this->getSerializeHandler()->setAttribute($key, $profile);
		}
		return $profile;
	}

	/**
	 * GETリクエスト
	 *
	 * @access public
	 * @param string $path パス
	 * @param BSParameterHolder $params パラメータの配列
	 * @return BSHTTPResponse レスポンス
	 */
	public function sendGET ($path = '/', BSParameterHolder $params = null) {
		$this->setAttribute('httpget', true);
		$request = $this->createRequest();
		if ($this->bearerToken) {
			$request->setHeader('Authorization', 'Bearer ' . $this->bearerToken);
		}
		$request->setMethod('GET');
		$request->setURL($this->createRequestURL($path));
		if ($params) {
			$request->getURL()->setParameter($params);
		}
		return $this->send($request);
	}

	/**
	 * POSTリクエスト
	 *
	 * @access public
	 * @param string $path パス
	 * @param BSRenderer $renderer レンダラー
	 * @return BSHTTPResponse レスポンス
	 */
	public function sendPOST ($path = '/', BSRenderer $renderer = null) {
		$request = $this->createRequest();
		if ($oauth = clone $this->oauth) {
			$oauth->setSeparator(', ');
			$request->setHeader('Authorization', 'OAuth ' . $oauth->getContents());
		}
		$request->setMethod('POST');
		$request->setURL($this->createRequestURL($path));
		$request->setRenderer($renderer);
		$this->setAttribute('post', true);
		$this->setAttribute('postfields', $request->getRenderer()->getContents());
		return $this->send($request);
	}

	protected function getSerializeHandler () {
		if (!$this->serializeHandler) {
			$this->serializeHandler = new BSSerializeHandler;
		}
		return $this->serializeHandler;
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
		$url['scheme'] = 'https';
		if (!$useragent) {
			$useragent = BSRequest::getInstance()->getUserAgent();
		}
		if ($useragent->isMobile()) {
			$url['host'] = 'mobile.twitter.com';
		} else {
			$url['host'] = 'twitter.com';
		}
		$url['path'] = '/' . $account . '/status/' . $id;
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
