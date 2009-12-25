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
class BSTinyURLService extends BSCurlHTTP {
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
	 * @param BSHTTPRedirector $link エンコード対象URL、又はそれを含んだリダイレクタ
	 * @return BSURL エンコードされたURL
	 */
	public function encode (BSHTTPRedirector $link) {
		$path = '/api-create.php?url=' . BSURL::encode($link->getURL()->getContents());
		$response = $this->sendGetRequest($path);
		return BSURL::getInstance($response->getRenderer()->getContents());
	}
}

/* vim:set tabstop=4: */
