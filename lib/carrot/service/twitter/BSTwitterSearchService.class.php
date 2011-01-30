<?php
/**
 * @package org.carrot-framework
 * @subpackage service.twitter
 */

/**
 * Twitter検索クライアント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSTwitterSearchService extends BSCurlHTTP {
	const DEFAULT_HOST = 'search.twitter.com';

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
	 * ツイートを検索
	 *
	 * @access public
	 * @param string $word 検索語
	 * @return BSArray ツイートの配列
	 */
	public function searchTweets ($word) {
		$tweets = new BSArray;
		try {
			$query = new BSWWWFormRenderer;
			$query['q'] = $word;
			$response = $this->sendGET('/search.json?' . $query->getContents());
			$json = new BSJSONRenderer;
			$json->setContents($response->getRenderer()->getContents());

			$result = $json->getResult();
			foreach ($result['results'] as $entry) {
				$entry = new BSArray($entry);
				$entry->removeParameter('result_type');
				$tweets[$entry['id']] = $entry;
			}
		} catch (Exception $e) {
			$tweets->clear();
		}
		return $tweets;
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('Twitter検索サービス "%s"', $this->getName());
	}
}

/* vim:set tabstop=4: */
