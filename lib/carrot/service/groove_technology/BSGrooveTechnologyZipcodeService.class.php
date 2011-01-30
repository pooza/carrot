<?php
/**
 * @package org.carrot-framework
 * @subpackage service.groove_technology
 */

/**
 * BSGrooveTechnology 郵便番号検索クライアント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @link http://groovetechnology.co.jp/webservice/zipsearch/
 */
class BSGrooveTechnologyZipcodeService extends BSCurlHTTP {
	const DEFAULT_HOST = 'groovetechnology.co.jp';

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
	 * 郵便番号を返す
	 *
	 * @access public
	 * @param string $address 住所
	 * @return BSZipcode 郵便番号
	 */
	public function getZipcode ($address) {
		try {
			$params = new BSWWWFormRenderer;
			$params['word'] = $address;
			$params['format'] = 'json';
			$path = '/ZipSearchService/v1/zipsearch?' . $params->getContents();
			$response = $this->sendGET($path);
			$serializer = new BSJSONSerializer;
			$result = $serializer->decode($response->getRenderer()->getContents());
			if(isset($result['zipcode']['a1'])) {
				return new BSZipcode($result['zipcode']['a1']['zipcode']);
			}
		} catch (Exception $e) {
		}
	}
}

/* vim:set tabstop=4: */
