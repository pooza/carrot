<?php
/**
 * @package org.carrot-framework
 * @subpackage service.groove_technology
 */

/**
 * GrooveTechnology 郵便番号検索 クライアント
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
	 * パスからリクエストURLを生成して返す
	 *
	 * @access public
	 * @param string $href パス
	 * @return BSHTTPURL リクエストURL
	 */
	public function createRequestURL ($href) {
		$url = parent::createRequestURL($href);
		$url->setParameter('format', 'json');
		return $url;
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
			$url = $this->createRequestURL('/ZipSearchService/v1/zipsearch');
			$url->setParameter('word', $address);
			$response = $this->sendGET($url->getFullPath());

			$serializer = new BSJSONSerializer;
			$result = $serializer->decode($response->getRenderer()->getContents());
			if(isset($result['zipcode']['a1'])) {
				return new BSZipcode($result['zipcode']['a1']['zipcode']);
			}
		} catch (Exception $e) {
		}
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('GrooveTechnology 郵便番号検索 "%s"', $this->getName());
	}
}

/* vim:set tabstop=4: */
