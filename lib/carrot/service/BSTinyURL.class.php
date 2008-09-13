<?php
/**
 * @package org.carrot-framework
 * @subpackage service
 */

/**
 * TinyURLクライアント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSTinyURL extends BSCurlHTTP {
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
	 * URLをエンコードする
	 *
	 * @access public
	 * @param BSURL $url エンコード対象URL
	 * @return BSURL エンコードされたURL
	 */
	public function encode (BSURL $url) {
		$path = '/api-create.php?url=' . urlencode($url->getContents());
		if ($result = $this->sendGetRequest($path)) {
			return new BSURL($result);
		}
	}
}

/* vim:set tabstop=4 ai: */
?>