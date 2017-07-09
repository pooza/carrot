<?php
/**
 * @package jp.co.b-shock.carrot
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
	 * @param integer $href URLのパス
	 * @param BSParameterHolder $params パラメータ配列
	 * @return BSDivisionElement
	 */
	public function createElement ($href, BSParameterHolder $params = null) {
		$params = BSArray::create($params);
		$element = new BSDivisionElement;
		if ($this->useragent->isMobile()) {
			$element->setBody('ケータイには非対応です。');
		} else {
			$info = $this->useragent->getDisplayInfo();
			if ($params['max_width'] && ($params['max_width'] < $params['width'])) {
				$params['width'] = $params['max_width'];
				$params['height'] = BSNumeric::round(
					$params['height'] * $params['width'] / $params['max_width']
				);
			}

			$iframe = $element->addElement(new BSXHTMLElement('iframe'));
			$url = BSURL::create('/embed/' . $href);
			$url['host'] = 'www.ustream.tv';
			$iframe->setAttribute('src', $url->getContents());
			$iframe->setAttribute('width', $params['width']);
			$iframe->setAttribute('height', $params['height']);
			$iframe->setAttribute('scrolling', 'no');
			$iframe->setAttribute('frameborder', '0');
			$iframe->setStyle('border', '0px none transparent');
			if ($params['align']) {
				$element->setStyle('width', $params['width']);
				$element = $element->setAlignment($params['align']);
			}
		}
		return $element;
	}

	/**
	 * チャンネル名から各種情報を返す
	 *
	 * @access public
	 * @param string $name チャンネル名
	 * @param BSParameterHolder $params パラメータ配列
	 * @return BSArray チャンネル情報の配列
	 */
	public function getChannelInfo ($name, BSParameterHolder $params = null) {
		$params = $this->createParameters($params);
		$key = get_class($this) . '.' . BSCrypt::digest([
			$name,
			$params->join("\n", "\t")
		]);

		$date = BSDate::getNow()->setParameter('hour', '-1');
		if (!$this->controller->getAttribute($key, $date)) {
			$url = $this->createRequestURL('/json');
			$url->setParameter('subject', 'channel');
			$url->setParameter('uid', $name);
			$url->setParameter('command', 'getCustomEmbedTag');
			$url->setParameter('params', $params->join(';', ':'));
			$response = $this->sendGET($url->getFullPath());

			$json = new BSJSONRenderer;
			$json->setContents($response->getRenderer()->getContents());
			$this->controller->setAttribute($key, $json->getResult());
		}
		return BSArray::create($this->controller->getAttribute($key));
	}

	private function createParameters ($src) {
		$dest = BSArray::create();
		if ($src) {
			foreach ($src as $key => $value) {
				if (in_array($key, ['width', 'height'])) {
					$dest[$key] = $value;
				}
				if (in_array($key, ['autoplay'])) {
					$dest[$key] = 'false';
					if (!!$value) {
						$dest[$key] = 'true';
					}
				}
			}
		}
		return $dest;
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('USTREAM "%s"', $this->getName());
	}
}

