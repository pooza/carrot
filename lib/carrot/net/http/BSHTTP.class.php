<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage http
 */

/**
 * HTTP処理
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSHTTP extends BSSocket {
	private $headers = array();
	protected $url;

	/**
	 * HEADリクエスト
	 *
	 * @access public
	 * @param string $path パス
	 * @return string[] ヘッダフィールドの配列
	 */
	public function sendHeadRequest ($path) {
		if ($this->isOpened()) {
			throw new BSHTTPException('%sは既に開いています。', $this);
		}

		$this->getURL()->setAttribute('path', $path);
		$this->open();
		$this->putLine('HEAD ' . $path . ' HTTP/1.0');
		$this->putLine('Host: ' . $this->getHost()->getName());
		$this->putLine();
		$this->setHeaders($this->getLines());
		$this->close();

		return $this->getHeaders();
	}

	/**
	 * GETリクエスト
	 *
	 * @access public
	 * @param string $path パス
	 * @return string レスポンスの本文
	 */
	public function sendGetRequest ($path) {
		$this->sendHeadRequest($path);
		return file_get_contents($this->getURL()->getContents());
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
		if ($this->isOpened()) {
			throw new BSHTTPException('%sは既に開いています。', $this);
		}

		$values = array();
		foreach ($params as $key => $value) {
			$values [] = $key . '=' . urlencode($value);
		}

		$this->getURL()->setAttribute('path', $path);
		$this->open();
		$this->putLine('POST ' . $path . ' HTTP/1.0');
		$this->putLine('Host: ' . $this->getHost()->getName());
		$this->putLine('User-Agent: ' . BSController::getFullName('en'));
		$this->putLine('Content-Type: application/x-www-form-urlencoded');
		$this->putLine('Content-Length: ' . strlen($values = implode('&', $values)));
		$this->putLine();
		$this->putLine($values);
		$response = implode("\n", $this->getLines());
		$this->close();

		$response = explode("\n\n", $response); //レスポンスを空行で区切る
		$this->setHeaders($response[0]);
		unset($response[0]);
		return implode("\n\n", $response);
	}

	/**
	 * 内容を返す
	 *
	 * sendGetRequestへのエイリアス
	 *
	 * @access public
	 * @param string $path ファイルのパス
	 * @return string 読み込んだ内容
	 * @final
	 */
	final public function getContents ($path = '/') {
		return $this->sendGetRequest($path);
	}

	/**
	 * ヘッダを返す
	 *
	 * @access public
	 * @return string[] ヘッダ
	 */
	public function getHeaders () {
		return $this->headers;
	}

	/**
	 * ヘッダを設定する
	 *
	 * @access protected
	 * @param mixed $headers ヘッダを含んだ文字列、又は配列
	 */
	protected function setHeaders ($headers) {
		if (!BSArray::isArray($headers)) {
			$headers = explode("\n", $headers); 
		}

		$this->headers = array();
		foreach ($headers as $header) {
			if (!$header) {
				break; //空行を見つけたらパースを中断
			} else if (preg_match('/^HTTP\/[0-9]\.[0-9] 4[0-9][0-9]/', $header)) {
				throw new BSHTTPException('%sで、"%s"が返されました。', $this, $header);
			} else if (preg_match("/([^:]+): (.*)$/", $header, $matches)) {
				$this->headers[$matches[1]] = $matches[2];
			}
		}
	}

	/**
	 * 直近のURLを返す
	 *
	 * @access public
	 * @return BSURL URL
	 */
	public function getURL () {
		if (!$this->url) {
			$this->url = new BSURL;
			$this->url->setAttribute('host', $this->getHost());
			$this->url->setAttribute('port', $this->getPort());
			$this->url->setAttribute('scheme', 'http');
		}
		return $this->url;
	}

	/**
	 * 基本情報を文字列で返す
	 *
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf(
			'HTTP接続 "%s:%d"',
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
		return BSServiceList::getPort('http');
	}
}

/* vim:set tabstop=4 ai: */
?>