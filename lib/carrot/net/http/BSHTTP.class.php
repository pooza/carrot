<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http
 */

/**
 * HTTPプロトコル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSHTTP extends BSSocket {

	/**
	 * HEADリクエスト
	 *
	 * @access public
	 * @param string $path パス
	 * @param BSParameterHolder $params パラメータの配列
	 * @return BSHTTPResponse レスポンス
	 */
	public function sendHEAD ($path = '/', BSParameterHolder $params = null) {
		$request = $this->createRequest();
		$request->setMethod('HEAD');
		$request->setURL($this->createRequestURL($path));
		if ($params) {
			$request->getURL()->setParameter($params);
		}
		return $this->send($request);
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
		$request = $this->createRequest();
		$request->setMethod('GET');
		$request->setURL($this->createRequestURL($path));
		if ($params) {
			$request->getURL()->setParameters($params);
		}
		return $this->send($request);
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
		return $this->send($request);
	}

	/**
	 * パスからリクエストURLを生成して返す
	 *
	 * @access public
	 * @param string $href パス
	 * @return BSHTTPURL リクエストURL
	 */
	public function createRequestURL ($href) {
		$url = BSURL::create();
		$url['host'] = $this->getHost();
		$url['path'] = '/' . ltrim($href, '/');
		if ($this->isSSL()) {
			$url['scheme'] = 'https';
		} else {
			$url['scheme'] = 'http';
		}
		return $url;
	}

	protected function createRequest () {
		$request = new BSHTTPRequest;
		$request->setHeader('User-Agent', BSController::getInstance()->getName('en'));
		return $request;
	}

	protected function send (BSHTTPRequest $request) {
		if ($this->isOpened()) {
			throw new BSHTTPException($this . 'は既に開いています。');
		}
		$this->putLine($request->getContents());
		$response = new BSHTTPResponse;
		$response->setContents($this->getLines()->join("\n"));
		$response->setURL($request->getURL());
		$this->log($response);
		return $response;
	}

	/**
	 * ログを出力
	 *
	 * @access protected
	 * @param BSHTTPResponse $response レスポンス
	 */
	protected function log (BSHTTPResponse $response) {
		if (BS_DEBUG || !$response->validate()) {
			$message = new BSStringFormat('%s に "%s" を送信しました。 (%s)');
			$message[] = $this;
			$message[] = $response->getURL()->getFullPath();
			$message[] = self::getStatus($response->getStatus());
			BSLogManager::getInstance()->put($message, $this);
		}
		if (!$response->validate()) {
			$message = new BSStringFormat('%sからのレスポンスが不正です。 (%d %s)');
			$message[] = $this;
			$message[] = $response->getStatus();
			$message[] = $response->getError();
			$exception = new BSHTTPException($message);
			$exception->setResponse($response);
			throw $exception;
		}
	}

	/**
	 * SSLモードか？
	 *
	 * SSLはサポートしない。必要ならば、BSCurlHTTPを使用すること。
	 *
	 * @access public
	 * @return boolean SSLモードならTrue
	 */
	public function isSSL () {
		return false;
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('HTTPソケット "%s"', $this->getName());
	}

	/**
	 * 規定のポート番号を返す
	 *
	 * @access public
	 * @return integer port
	 */
	public function getDefaultPort () {
		return BSNetworkService::getPort('http');
	}

	/**
	 * 全てのステータスを返す
	 *
	 * @access public
	 * @return BSArray 全てのステータス
	 * @static
	 */
	static public function getAllStatus () {
		return new BSArray(BSConfigManager::getInstance()->compile('http_status'));
	}

	/**
	 * ステータスを返す
	 *
	 * @access public
	 * @param integer $code ステータスコード
	 * @return string ステータス文字列
	 * @static
	 */
	static public function getStatus ($code) {
		if ($status = self::getAllStatus()->getParameter($code)) {
			return $code . ' ' . $status['status'];
		}

		$message = new BSStringFormat('ステータスコード "%d" が正しくありません。');
		$message[] = $code;
		throw new BSHTTPException($message);
	}
}

/* vim:set tabstop=4: */
