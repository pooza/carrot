<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http
 */

/**
 * HTTPプロトコル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSHTTP extends BSSocket {
	private $status;

	/**
	 * HEADリクエスト
	 *
	 * @access public
	 * @param string $path パス
	 * @return BSHTTPResponse レスポンス
	 */
	public function sendHeadRequest ($path = '/') {
		$url = BSURL::getInstance();
		$url['host'] = $this->getHost();
		$url['path'] = $path;

		$request = new BSHTTPRequest;
		$request->setMethod('HEAD');
		$request->setURL($url);
		return $this->send($request);
	}

	/**
	 * GETリクエスト
	 *
	 * @access public
	 * @param string $path パス
	 * @return BSHTTPResponse レスポンス
	 */
	public function sendGetRequest ($path = '/') {
		$url = BSURL::getInstance();
		$url['host'] = $this->getHost();
		$url['path'] = $path;

		$request = new BSHTTPRequest;
		$request->setMethod('GET');
		$request->setURL($url);
		return $this->send($request);
	}

	/**
	 * POSTリクエスト
	 *
	 * @access public
	 * @param string $path パス
	 * @param string[] $params パラメータの配列
	 * @return BSHTTPResponse レスポンス
	 */
	public function sendPostRequest ($path = '/', $params = array()) {
		$url = BSURL::getInstance();
		$url['host'] = $this->getHost();
		$url['path'] = $path;

		$request = new BSHTTPRequest;
		$request->setMethod('POST');
		$request->setRenderer(new BSWWWFormRenderer);
		$request->getRenderer()->setParameters($params);
		$request->removeHeader('Content-Transfer-Encoding');
		$request->setURL($url);
		return $this->send($request);
	}

	/**
	 * リクエストを実行し、結果を返す。
	 *
	 * @access protected
	 * @param BSHTTPRequest $request リクエスト
	 * @return BSHTTPResponse 結果文書
	 */
	protected function send (BSHTTPRequest $request) {
		if ($this->isOpened()) {
			throw new BSHTTPException($this . 'は既に開いています。');
		}

		$request->setHeader('User-Agent', BSController::getFullName('en'));
		$this->putLine($request->getContents());

		$response = new BSHTTPResponse;
		$response->setContents($this->getLines());
		$response->setURL($request->getURL());

		if (!$response->validate()) {
			$message = new BSStringFormat('不正なレスポンスです。 (%d %s)');
			$message[] = $response->getStatus();
			$message[] = $response->getError();
			throw new BSHTTPException($message);
		}
		return $response;
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
