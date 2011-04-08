<?php
/**
 * @package org.carrot-framework
 * @subpackage service.oauth
 */

/**
 * OAuthシグネチャ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSOAuthSignature extends BSParameterHolder {
	private $method;
	private $url;
	private $request;
	private $consumer;

	/**
	 * @access public
	 */
	public function __construct (BSRequest $request) {
		$this->setMethod('GET');
		$this->setURL(BSURL::create());
		$this->request = $request;
		$this['oauth_signature_method'] = 'HMAC-SHA1';
		$this['oauth_version'] = '1.0';
		$this['oauth_nonce'] = BSDate::getNow('YmdHis') . BSNumeric::getRandom(1000, 9999);
		$this['oauth_timestamp'] = BSDate::getNow()->getTimestamp();

		if ($header = $this->request->getHeader('Authorization')) {
			foreach (array('token', 'token_secret', 'nonce', 'timestamp') as $name) {
				$name = 'oauth_' . $name;
				if (!BSString::isBlank($value = $header[$name])) {
					$this[$name] = $value;
				}
			}
		}
	}

	/**
	 * シグネチャを返す
	 *
	 * @access public
	 * @return string シグネチャ
	 */
	public function getContents () {
		return base64_encode(hash_hmac(
			'sha1',
			$this->getBaseString(),
			$this->getKey(),
			true
		));
	}

	/**
	 * ベース文字列を返す
	 *
	 * @access protected
	 * @return string ベース文字列
	 */
	protected function getBaseString () {
		$url = clone $this->url;
		$url['query'] = null;

		$params = new BSArray($this->getParameters());
		$params->setParameters($this->url->getParameters());
		$params->sort();
		$query = new BSWWWFormRenderer;
		$query->setParameters($params);

		$values = new BSArray(array(
			$this->method,
			$url->getContents(),
			$query->getContents(),
		));
		$values = BSURL::encode($values);
		return $values->join('&');
	}

	/**
	 * シグネチャ生成用のキーを返す
	 *
	 * @access protected
	 * @return string キー
	 */
	protected function getKey () {
		$values = new BSArray;
		if ($this->consumer) {
			$values[] = $this->consumer->getSecret();
		}
		if ($header = $this->request->getHeader('Authorization')) {
			$values[] = $header['oauth_token_secret'];
		}
		$values = BSURL::encode($values);
		return $values->join('&');
	}

	/**
	 * メソッドを設定
	 *
	 * @access public
	 * @param string $method メソッド
	 */
	public function setMethod ($method) {
		$this->method = BSString::toUpper($method);
		if (!BSHTTPRequest::isValidMethod($this->method)) {
			throw new BSServiceException($this->method . 'は正しくないメソッドです。');
		}
	}

	/**
	 * コンシューマを設定
	 *
	 * @access public
	 * @param BSOAuthConsumer $consumer コンシューマ
	 */
	public function setConsumer (BSOAuthConsumer $consumer) {
		$this->consumer = $consumer;
		$this['oauth_consumer_key'] = $consumer->getKey();
	}

	/**
	 * 宛先URlを設定
	 *
	 * @access public
	 * @param BSHTTPRedirector $url URL
	 */
	public function setURL (BSHTTPRedirector $url) {
		$this->url = clone $url->getURL();
	}
}

/* vim:set tabstop=4: */
