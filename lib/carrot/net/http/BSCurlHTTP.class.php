<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http
 */

/**
 * CurlによるHTTP処理
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSCurlHTTP extends BSHTTP {
	protected $engine;
	protected $attributes;
	protected $headers;
	protected $uid;
	protected $password;
	protected $ssl = false;

	/**
	 * @access public
	 * @param mixed $host ホスト
	 * @param integer $port ポート
	 * @param string $protocol プロトコル
	 *   BSNetworkService::TCP
	 *   BSNetworkService::UDP
	 */
	public function __construct ($host, $port = null, $protocol = BSNetworkService::TCP) {
		parent::__construct($host, $port, $protocol);
		$this->attributes = new BSArray;
		$this->headers = new BSArray;
	}

	/**
	 * HEADリクエスト
	 *
	 * @access public
	 * @param string $path パス
	 * @return BSHTTPResponse レスポンス
	 */
	public function sendHEAD ($path = '/') {
		$this->setAttribute('nobody', true);
		return $this->execute($path);
	}

	/**
	 * GETリクエスト
	 *
	 * @access public
	 * @param string $path パス
	 * @return BSHTTPResponse レスポンス
	 */
	public function sendGET ($path = '/') {
		$this->setAttribute('httpget', true);
		return $this->execute($path);
	}

	/**
	 * POSTリクエスト
	 *
	 * @access public
	 * @param string $path パス
	 * @param BSParameterHolder $params パラメータの配列
	 * @return BSHTTPResponse レスポンス
	 */
	public function sendPOST ($path = '/', BSParameterHolder $params = null) {
		$renderer = new BSWWWFormRenderer;
		$renderer->setParameters($params);

		$this->setAttribute('post', true);
		$this->setAttribute('postfields', $renderer->getContents());
		return $this->execute($path);
	}

	/**
	 * リクエスト実行
	 *
	 * @param string $path パス
	 * @access protected
	 * @return BSHTTPResponse レスポンス
	 */
	protected function execute ($path) {
		$url = $this->createRequestURL($path);
		$this->setAttribute('url', $url->getContents());

		$headers = array();
		foreach ($this->headers as $header) {
			$headers[] = $header->getName() . ': ' . $header->getContents();
		}
		$this->setAttribute('httpheader', $headers);

		$response = new BSHTTPResponse;
		$response->setURL($url);
		if (($contents = curl_exec($this->getEngine())) === false) {
			throw new BSHTTPException($url . 'へ送信できません。');
		}
		$response->setContents($contents);

		if (!$response->validate()) {
			$message = new BSStringFormat('%sからのレスポンスが不正です。 (%d %s)');
			$message[] = $this;
			$message[] = $response->getStatus();
			$message[] = $response->getError();
			$exception = new BSHTTPException($message);
			$exception->setResponse($response);
			throw $exception;
		}
		return $response;
	}

	/**
	 * パスからリクエストURLを生成して返す
	 *
	 * @param string $href パス
	 * @access protected
	 * @return BSHTTPURL リクエストURL
	 */
	protected function createRequestURL ($href) {
		$url = BSURL::getInstance();
		$url['host'] = $this->getHost();
		$url['path'] ='/' . ltrim($href, '/');
		if ($this->isSSL()) {
			$url['scheme'] = 'https';
			$url['port'] = BSNetworkService::getPort('https');
		} else {
			$url['scheme'] = 'http';
			$url['port'] = BSNetworkService::getPort('http');
		}
		return $url;
	}

	/**
	 * Curlエンジンを返す
	 *
	 * @access private
	 * @return handle Curlエンジン
	 */
	private function getEngine () {
		if (!$this->engine) {
			if (!extension_loaded('curl')) {
				throw new BSHTTPException('curlモジュールがロードされていません。');
			}

			$this->engine = curl_init();
			$this->setAttribute('autoreferer', true);
			$this->setAttribute('useragent', BSController::getInstance()->getName('en'));
			$this->setAttribute('followlocation', true);
			$this->setAttribute('header', true);
			$this->setAttribute('returntransfer', true);
			$this->setAttribute('maxredirs', 32);
			$this->setAttribute('ssl_verifypeer', false);
			if (!$this->host->isForeign()) {
				$this->setAuth(BS_APP_BASIC_AUTH_UID, BS_APP_BASIC_AUTH_PASSWORD);
			}
		}
		return $this->engine;
	}

	/**
	 * 属性を返す
	 *
	 * @access public
	 * @param string $name 属性名
	 * @return mixed 属性値
	 */
	public function getAttribute ($name) {
		$names = array(
			'curlopt_' . $name,
			'curl_' . $name,
			$name,
		);
		foreach ($names as $name) {
			if ($this->attributes->hasParameter($name)) {
				return $this->attributes[$name];
			}
		}
	}

	/**
	 * 属性を設定
	 *
	 * @access public
	 * @param string $name 属性名
	 * @param mixed $value 属性値
	 */
	public function setAttribute ($name, $value) {
		if (!$this->getEngine()) {
			return;
		}

		$names = array(
			'curlopt_' . $name,
			'curl_' . $name,
			$name,
		);
		$constants = BSConstantHandler::getInstance();
		foreach ($names as $name) {
			if ($constants->hasParameter($name)) {
				$this->attributes[$name] = $value;
				curl_setopt($this->getEngine(), $constants[$name], $value);
				return;
			}
		}
	}

	/**
	 * ヘッダを設定
	 *
	 * @access public
	 * @param BSMIMEHeader $header ヘッダ
	 */
	public function setHeader (BSMIMEHeader $header) {
		$this->headers[$header->getName()] = $header;
	}

	/**
	 * HTTP認証のアカウントを設定
	 *
	 * @access public
	 * @param string $uid ユーザー名
	 * @param string $password BSCryptで暗号化されたパスワード
	 */
	public function setAuth ($uid, $password) {
		if (BSString::isBlank($password)) {
			return;
		}
		$this->uid = $uid;
		$this->password = BSCrypt::getInstance()->decrypt($password);
		$this->setAttribute('userpwd', $this->uid . ':' . $this->password);
	}

	/**
	 * SSLモードか？
	 *
	 * @access public
	 * @return boolean SSLモードならTrue
	 */
	public function isSSL () {
		return $this->ssl;
	}

	/**
	 * SSLモードを設定
	 *
	 * @access public
	 * @param boolean $mode SSLモード
	 */
	public function setSSL ($mode) {
		$this->ssl = !!$mode;
		$this->name = null;
		if ($this->isSSL()) {
			$this->port = BSNetworkService::getPort('https');
		} else {
			$this->port = BSNetworkService::getPort('http');
		}
	}
}

/* vim:set tabstop=4: */
