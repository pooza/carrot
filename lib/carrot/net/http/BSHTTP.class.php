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
	private $headers = array();
	private $status;
	protected $response;

	/**
	 * 直前のレスポンスを返す
	 *
	 * @access public
	 * @return BSHTTPResponse 直前のレスポンス
	 */
	public function getResponse () {
		return clone $this->response;
	}

	/**
	 * HEADリクエスト
	 *
	 * @access public
	 * @param string $path パス
	 * @return BSHTTPResponse レスポンス
	 */
	public function sendHeadRequest ($path = '/') {
		$url = new BSURL;
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
		$url = new BSURL;
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
		$url = new BSURL;
		$url['host'] = $this->getHost();
		$url['path'] = $path;

		$request = new BSHTTPRequest;
		$request->setMethod('POST');
		$request->setHeader('User-Agent', BSController::getFullName('en'));
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
			throw new BSHTTPException('%sは既に開いています。', $this);
		}

		$this->putLine($request->getContents());
		$this->response = new BSHTTPResponse;
		$this->response->setContents(new BSArray($this->getLines()));
		$this->response->setURL($request->getURL());
	
		if (!$this->response->validate()) {
			throw new BSHTTPException(
				'不正なレスポンスです。 (%d %s)',
				$this->response->getStatus(),
				$this->response->getError()
			);
		}
		return $this->response;
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf(
			'HTTPサーバ "%s:%d"',
			$this->getHost()->getName(),
			$this->getPort()
		);
	}

	/**
	 * 規定のポート番号を返す
	 *
	 * @access public
	 * @return integer port
	 * @static
	 */
	static public function getDefaultPort () {
		return BSNetworkService::getPort('http');
	}
}

/* vim:set tabstop=4: */
