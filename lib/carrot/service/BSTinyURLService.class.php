<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage service
 */

/**
 * TinyURLクライアント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSTinyURLService extends BSCurlHTTP implements BSURLShorter {
	const DEFAULT_HOST = 'tinyurl.com';

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
	 * 短縮URLを返す
	 *
	 * @access public
	 * @param BSHTTPRedirector $url 対象URL
	 * @return BSHTTPURL 短縮URL
	 */
	public function getShortURL (BSHTTPRedirector $url) {
		$url = $this->createRequestURL('/api-create.php');
		$url->setParameter('url', $url->getURL()->getContents());
		$response = $this->sendGET($url->getFullPath());
		return BSURL::create($response->getRenderer()->getContents());
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('TinyURL "%s"', $this->getName());
	}
}

/* vim:set tabstop=4: */
