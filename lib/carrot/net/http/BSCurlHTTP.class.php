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
		return parent::sendHEAD($path);
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
		return parent::sendGET($path);
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
		$request = $this->createRequest();
		$request->setMethod('POST');
		$request->setRenderer(new BSWWWFormRenderer);
		$request->getRenderer()->setParameters($params);
		$request->setURL($this->createRequestURL($path));
		$this->setAttribute('post', true);
		$this->setAttribute('postfields', $request->getRenderer()->getContents());
		return $this->send($request);
	}

	protected function send (BSHTTPRequest $request) {
		$request->removeHeader('Content-Transfer-Encoding');
		$headers = array();
		foreach ($request->getHeaders() as $header) {
			$headers[] = $header->getName() . ': ' . $header->getContents();
		}
		$this->setAttribute('httpheader', $headers);
		$this->setAttribute('url', $request->getURL()->getContents());

		$response = new BSHTTPResponse;
		$response->setURL($request->getURL());
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
	 * Curlエンジンを返す
	 *
	 * @access protected
	 * @return handle Curlエンジン
	 */
	protected function getEngine () {
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
