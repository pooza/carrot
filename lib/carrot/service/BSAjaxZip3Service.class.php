<?php
/**
 * @package org.carrot-framework
 * @subpackage service
 */

/**
 * AjaxZip3クライアント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @link http://code.google.com/p/ajaxzip3/
 */
class BSAjaxZip3Service extends BSCurlHTTP {
	const DEFAULT_HOST = 'ajaxzip3.googlecode.com';

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
	 * 住所情報を返す
	 *
	 * @access public
	 * @param string $prefix 先頭3桁
	 * @return BSArray 住所情報
	 */
	public function getAddresses ($prefix) {
		$url = $this->createRequestURL(BS_SERVICE_AJAXZIP3_ZIPDATA_HREF);
		$url['path'] .= 'zip-' . $prefix . '.js';
		$response = $this->sendGET($url->getFullPath());

		$contents = $response->getRenderer()->getContents();
		$contents = mb_ereg_replace('^[[:alpha:]]+\\(', null, $contents);
		$contents = mb_ereg_replace('\\);[[:space:]]*$', null, $contents);
		return new BSArray(json_decode($contents, true));
	}
}

/* vim:set tabstop=4: */
