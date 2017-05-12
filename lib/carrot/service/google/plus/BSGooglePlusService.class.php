<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage service.google.plus
 */

/**
 * Google+クライアント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSGooglePlusService extends BSCurlHTTP {
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
		$url->setParameter('key', BS_SERVICE_GOOGLE_PLUS_API_KEY);
		return $url;
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('Google+サービス "%s"', $this->getName());
	}
}

