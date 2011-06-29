<?php
/**
 * @package org.carrot-framework
 * @subpackage service
 */

/**
 * USTREAMクライアント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSUstreamService extends BSCurlHTTP {
	private $useragent;
	const DEFAULT_HOST = 'api.ustream.tv';

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
	 * パスからリクエストURLを生成して返す
	 *
	 * @access public
	 * @param string $href パス
	 * @return BSHTTPURL リクエストURL
	 */
	public function createRequestURL ($href) {
		$url = parent::createRequestURL($href);
		$url->setParameter('key', BS_SERVICE_USTREAM_API_KEY);
		return $url;
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
		$element = new BSDivisionElement;
		if ($this->useragent->isMobile()) {
			$element->setBody('ケータイには非対応です。');
		} else {
			$info = $this->getChannelInfo($id);
			$object = $element->addElement(new BSObjectElement);
			$object->setContents($info['embedTag']);
			$object->setStyle('width', $object->getAttribute('width'));
			$object->setStyle('height', $object->getAttribute('height'));
			$element = $element->setAlignment($params['align']);
		}
		return $element;
	}

	/**
	 * チャンネル名から各種情報を返す
	 *
	 * @access public
	 * @param string $name チャンネル名
	 * @return BSArray チャンネル情報の配列
	 */
	public function getChannelInfo ($name) {
		$key = get_class($this) . '.' . $name;
		$controller = BSController::getInstance();
		$date = BSDate::getNow()->setAttribute('hour', '-1');
		if (!$controller->getAttribute($key, $date)) {
			$url = $this->createRequestURL('/json');
			$url->setParameter('subject', 'channel');
			$url->setParameter('uid', $name);
			$url->setParameter('command', 'getinfo');
			$response = $this->sendGET($url->getFullPath());

			$json = new BSJSONRenderer;
			$json->setContents($response->getRenderer()->getContents());
			$result = $json->getResult();
			$controller->setAttribute($key, $result['results']);
		}
		return new BSArray($controller->getAttribute($key));
	}
}

/* vim:set tabstop=4: */
