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
		$header = BSMIMEHeader::getInstance('Content-Type');
		$header->setContents('application/json');
		$this->setHeader($header);

		$renderer = new BSJSONRenderer;
		$renderer->setContents(new BSArray($params));
		$this->setAttribute('post', true);
		$this->setAttribute('postfields', $renderer->getContents());
		return $this->execute($path);
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
		$url = BSURL::create();
		$url['host'] = $this->getHost();
		$url['path'] = '/urlshortener/v1/url';
		$url->setParameter('key', BS_SERVICE_GOOGLE_URL_SHORTENER_API_KEY);
		$response = $this->sendPOST($url->getFullPath(), $params);

		$json = new BSJSONSerializer;
		$result = $json->decode($response->getRenderer()->getContents());
		return BSURL::create($result['id']);
	}
}

/* vim:set tabstop=4: */
