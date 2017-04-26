<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net.http
 */

/**
 * CurlによるHTTP処理
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSCurlHTTP extends BSHTTP {
	protected $engine;
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
		if ($port == BSNetworkService::getPort('https')) {
			$this->setSSL(true);
		}
		$this->attributes = new BSArray;
	}

	/**
	 * HEADリクエスト
	 *
	 * @access public
	 * @param string $path パス
	 * @param BSParameterHolder $params パラメータの配列
	 * @return BSHTTPResponse レスポンス
	 */
	public function sendHEAD ($path = '/', BSParameterHolder $params = null) {
		$this->setAttribute('nobody', true);
		return parent::sendHEAD($path, $params);
	}

	/**
	 * GETリクエスト
	 *
	 * @access public
	 * @param string $path パス
	 * @param BSParameterHolder $params パラメータの配列
	 * @return BSHTTPResponse レスポンス
	 */
	public function sendGET ($path = '/', BSParameterHolder $params = null) {
		$this->setAttribute('httpget', true);
		return parent::sendGET($path, $params);
	}

	/**
	 * POSTリクエスト
	 *
	 * @access public
	 * @param string $path パス
	 * @param BSRenderer $renderer レンダラー
	 * @return BSHTTPResponse レスポンス
	 */
	public function sendPOST ($path = '/', BSRenderer $renderer = null) {
		$request = $this->createRequest();
		$request->setMethod('POST');
		$request->setRenderer($renderer);
		$request->setURL($this->createRequestURL($path));
		$this->setAttribute('post', true);
		$this->setAttribute('postfields', $request->getRenderer()->getContents());
		return $this->send($request);
	}

	protected function send (BSHTTPRequest $request) {
		$headers = [];
		foreach ($request->getHeaders() as $header) {
			$headers[] = $header->getName() . ': ' . $header->getContents();
		}
		$this->setAttribute('httpheader', $headers);
		$this->setAttribute('url', $request->getURL()->getContents());

		$response = new BSHTTPResponse;
		$response->setURL($request->getURL());
		if (($contents = curl_exec($this->getEngine())) === false) {
			throw new BSHTTPException($request->getURL() . 'へ送信できません。');
		}
		$response->setContents($contents);
		$this->log($response);
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
			$this->setAttribute('useragent', $this->controller->getName('en'));
			$this->setAttribute('followlocation', true);
			$this->setAttribute('header', true);
			$this->setAttribute('returntransfer', true);
			$this->setAttribute('maxredirs', 32);
			$this->setAttribute('ssl_verifypeer', false);
		}
		return $this->engine;
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
		foreach (['curlopt', 'curl', null] as $prefix) {
			$constants = new BSConstantHandler($prefix);
			if ($constants->hasParameter($name)) {
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
	}
}

/* vim:set tabstop=4: */
