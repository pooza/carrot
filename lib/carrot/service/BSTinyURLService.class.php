<?php
/**
 * @package org.carrot-framework
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
		$path = '/api-create.php?url=' . BSURL::encode($url->getURL()->getContents());
		$response = $this->sendGET($path);
		return BSURL::create($response->getRenderer()->getContents());
	}
}

/* vim:set tabstop=4: */
