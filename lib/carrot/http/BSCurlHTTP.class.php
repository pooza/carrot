<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage http
 */

/**
 * CurlによるHTTP処理
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSCurlHTTP extends BSHTTP {
	private $engine;
	private $attributes = array();
	private $ssl = false;

	/**
	 * デストラクタ
	 *
	 * @access public
	 */
	public function __destruct () {
		if ($this->getEngine()) {
			curl_close($this->getEngine());
		}
	}

	/**
	 * GETリクエスト
	 *
	 * @access public
	 * @param string $path パス
	 * @return string レスポンスの本文
	 */
	public function sendGetRequest ($path) {
		if (!$this->getEngine()) {
			return parent::sendGetRequest($path);
		}

		$this->getURL()->setAttribute('path', $path);
		$this->setAttribute('httpget', true);
		$this->setAttribute('post', false);
		return $this->execute();
	}

	/**
	 * POSTリクエスト
	 *
	 * @access public
	 * @param string $path パス
	 * @param string[] $params パラメータの配列
	 * @return string レスポンスの本文
	 */
	public function sendPostRequest ($path, $params = array()) {
		if (!$this->getEngine()) {
			return parent::sendPostRequest($path, $params);
		}

		$values = array();
		foreach ($params as $key => $value) {
			$values[] = $key . '=' . $value;
		}

		$this->getURL()->setAttribute('path', $path);
		$this->setAttribute('post', true);
		$this->setAttribute('httpget', false);
		$this->setAttribute('postfields', implode('&', $values));
		return $this->execute();
	}

	/**
	 * リクエスト実行
	 *
	 * @return string 実行結果
	 * @access private
	 */
	private function execute () {
		$this->setAttribute('url', $this->getURL()->getContents());
		$response = curl_exec($this->getEngine());
		$response = str_replace(self::LINE_SEPARATOR, "\n", $response);
		$response = explode("\n\n", $response);
		$this->setHeaders($response[0]);
		unset($response[0]);
		return implode("\n\n", $response);
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
				return null;
			}

			$this->engine = curl_init();
			$this->setAttribute('autoreferer', true);
			$this->setAttribute('useragent', BSController::getFullName('en'));
			$this->setAttribute('followlocation', true);
			$this->setAttribute('header', true);
			$this->setAttribute('returntransfer', true);
			$this->setAttribute('maxredirs', 32);
			$this->setAttribute('ssl_verifypeer', false);
		}
		return $this->engine;
	}

	/**
	 * 直近のURLを返す
	 *
	 * @access public
	 * @return BSURL URL
	 */
	public function getURL () {
		if (!$this->url) {
			$this->url = new BSURL();
			$this->url->setAttribute('host', $this->getHost());
			$this->url->setAttribute('port', $this->getPort());
			if ($this->isSSL()) {
				$this->url->setAttribute('scheme', 'https');
			} else {
				$this->url->setAttribute('scheme', 'http');
			}
		}
		return $this->url;
	}

	/**
	 * 全ての属性を返す
	 *
	 * @access public
	 * @return mixed[] 全ての属性
	 */
	public function getAttributes () {
		return $this->attributes;
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
		foreach ($names as $name) {
			if (defined($name = strtoupper($name))) {
				$this->attributes[$name] = $value;
				curl_setopt($this->getEngine(), constant($name), $value);
				return;
			}
		}
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
	 * SSLモードを設定する
	 *
	 * @access public
	 * @param boolean $mode SSLモード
	 */
	public function setSSL ($mode) {
		$this->ssl = $mode;
		if ($this->isSSL()) {
			if (!$this->getEngine()) {
				throw new BSNetException('SSLモードの実行にはCurlが必要です。');
			}
			$this->setPort(BSServiceList::getPort('https'));
			$this->getURL()->setAttribute('scheme', 'https');
		} else {
			$this->setPort(BSServiceList::getPort('http'));
			$this->getURL()->setAttribute('scheme', 'http');
		}
	}
}

/* vim:set tabstop=4 ai: */
?>