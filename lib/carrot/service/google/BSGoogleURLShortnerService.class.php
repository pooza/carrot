<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage service.google
 */

/**
 * Google URL Shortnerクライアント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSGoogleURLShortnerService extends BSCurlHTTP implements BSURLShorter {
	const DEFAULT_HOST = 'www.googleapis.com';

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
	 * パスからリクエストURLを生成して返す
	 *
	 * @access public
	 * @param string $href パス
	 * @return BSHTTPURL リクエストURL
	 */
	public function createRequestURL ($href) {
		$url = parent::createRequestURL($href);
		$url->setParameter('key', BS_SERVICE_GOOGLE_URL_SHORTENER_API_KEY);
		return $url;
	}

	/**
	 * 短縮URLを返す
	 *
	 * @access public
	 * @param BSHTTPRedirector $url 対象URL
	 * @return BSHTTPURL 短縮URL
	 */
	public function getShortURL (BSHTTPRedirector $url) {
		$json = new BSJSONRenderer;
		$json->setContents([
			'longUrl' => $url->getURL()->getContents(),
		]);
		$url = $this->createRequestURL('/urlshortener/v1/url');
		$response = $this->sendPOST($url->getFullPath(), $json);

		$json = new BSJSONSerializer;
		$result = $json->decode($response->getRenderer()->getContents());
		return BSURL::create($result['id']);
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('Google URL Shortner "%s"', $this->getName());
	}
}

/* vim:set tabstop=4: */
