<?php
/**
 * @package org.carrot-framework
 * @subpackage service
 */

/**
 * bit.lyクライアント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSBitlyService extends BSCurlHTTP implements BSURLShorter {
	const DEFAULT_HOST = 'api.bit.ly';

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
	 * パスからリクエストURLを生成して返す
	 *
	 * @access public
	 * @param string $href パス
	 * @return BSHTTPURL リクエストURL
	 */
	public function createRequestURL ($href) {
		$url = parent::createRequestURL($href);
		$url->setParameter('version', BS_SERVICE_BITLY_VERSION);
		$url->setParameter('login', BS_SERVICE_BITLY_LOGIN);
		$url->setParameter('apiKey', BS_SERVICE_BITLY_API_KEY);
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
		$request = $this->createRequestURL('shorten');
		$request->setParameter('longUrl', $url->getContents());
		$response = $this->sendGET($request->getFullPath());

		$json = new BSJSONSerializer;
		$result = $json->decode($response->getRenderer()->getContents());
		$result = new BSArray($result['results']);
		$result = new BSArray($result->getIterator()->getFirst());
		return BSURL::create($result['shortUrl']);
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('bit.ly "%s"', $this->getName());
	}
}

/* vim:set tabstop=4: */
