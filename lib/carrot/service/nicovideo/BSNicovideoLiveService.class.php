<?php
/**
 * @package org.carrot-framework
 * @subpackage service.nicovideo
 */

/**
 * YouTubeクライアント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSNicovideoLiveService extends BSCurlHTTP {
	private $useragent;
	const DEFAULT_HOST = 'live.nicovideo.jp';

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
		$this->useragent = BSRequest::getInstance()->getUserAgent();
	}

	/**
	 * 対象UserAgentを設定
	 *
	 * @access public
	 * @param BSUserAgent $useragent 対象UserAgent
	 */
	public function setUserAgent (BSUserAgent $useragent) {
		$this->useragent = $useragent;
	}

	/**
	 * 要素を返す
	 *
	 * @access public
	 * @param integer $id ビデオID
	 * @param BSParameterHolder $params パラメータ配列
	 * @return BSDivisionElement
	 */
	public function createElement ($id, BSParameterHolder $params = null) {
		$params = new BSArray($params);
		if ($this->useragent->isMobile()) {
			$element = new BSDivisionElement;
			$element->setBody('ケータイには非対応です。');
		} else {
			$element = new BSNicovideoLiveInlineFrameElement(null, $this->useragent);
			$element->setChannel($id);
			$element = $element->setAlignment($params['align']);
		}
		return $element;
	}
}

/* vim:set tabstop=4: */
