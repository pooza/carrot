<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage service.google
 */

/**
 * YouTubeクライアント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSYouTubeService extends BSCurlHTTP {
	private $useragent;
	const DEFAULT_HOST = 'www.youtube.com';
	const DEFAULT_HOST_MOBILE = 'm.youtube.com';

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
		$this->useragent = $this->request->getUserAgent();
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
		$params = BSArray::create($params);
		if ($this->useragent->isMobile()) {
			if (BSString::isBlank($params['label'])) {
				$params['label'] = '動画再生';
			}
			$element = new BSAnchorElement;
			$element->setTargetBlank(true);
			$element->setBody($params['label']);
			$element->setURL($this->createPageURL($id));
		} else {
			$params->removeParameter('label');
			$info = $this->useragent->getDisplayInfo();
			if ($params['max_width'] && ($params['max_width'] < $params['width'])) {
				$params['width'] = $params['max_width'];
				$params['height'] = BSNumeric::round(
					$params['height'] * $params['width'] / $params['max_width']
				);
			}
			$element = new BSYouTubeObjectElement(null, $this->useragent);
			$element->setMovie($id, $params);
			$element->setAttribute('width', $params['width']);
			$element->setAttribute('height', $params['height']);
			if ($params['align']) {
				$element->setStyle('width', $params['width']);
				$element = $element->setAlignment($params['align']);
			}
		}
		return $element;
	}

	private function createPageURL ($id) {
		$url = BSURL::create();
		if ($this->useragent->isMobile()) {
			$url['host'] = self::DEFAULT_HOST_MOBILE;
		} else {
			$url['host'] = self::DEFAULT_HOST;
		}
		$url['path'] = '/watch';
		$url->setParameter('v', $id);
		return $url;
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('YouTube "%s"', $this->getName());
	}
}

