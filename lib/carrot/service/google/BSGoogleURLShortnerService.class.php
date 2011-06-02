<?php
/**
 * @package org.carrot-framework
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
			$this->setSSL(true);
		}
		parent::__construct($host, $port);
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
		$request = $this->createRequest();
		$request->setMethod('POST');
		$request->setRenderer(new BSJSONRenderer);
		$request->getRenderer()->setContents(new BSArray($params));
		$request->setURL($this->createRequestURL($path));
		$this->setAttribute('post', true);
		$this->setAttribute('postfields', $request->getRenderer()->getContents());
		return $this->send($request);
	}

	/**
	 * パスからリクエストURLを生成して返す
	 *
	 * @access protected
	 * @param string $href パス
	 * @return BSHTTPURL リクエストURL
	 */
	protected function createRequestURL ($href) {
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
		$params = new BSArray(array(
			'longUrl' => $url->getURL()->getContents(),
		));
		$response = $this->sendPOST(
			$this->createRequestURL('/urlshortener/v1/url')->getFullPath(),
			$params
		);

		$json = new BSJSONSerializer;
		$result = $json->decode($response->getRenderer()->getContents());
		return BSURL::create($result['id']);
	}
}

/* vim:set tabstop=4: */
