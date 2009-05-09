<?php
/**
 * @package org.carrot-framework
 * @subpackage net
 */

/**
 * ソケット
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSSocket {
	private $client;
	private $name;
	private $host;
	private $port;
	private $line;
	const LINE_BUFFER = 4096;
	const RETRY_LIMIT = 10;
	const LINE_SEPARATOR = "\r\n";

	/**
	 * @access public
	 * @param mixed $host ホスト
	 * @param integer $port ポート
	 */
	public function __construct ($host, $port = null) {
		$this->setHost($host);
		$this->setPort($port);
	}

	/**
	 * @access public
	 */
	public function __destruct () {
		if ($this->isOpened()) {
			$this->close();
		}
	}

	/**
	 * 名前を返す
	 *
	 * @access public
	 * @return string 名前
	 */
	public function getName () {
		if (!$this->name) {
			$host = new BSStringFormat('%s://%s:%s');
			$host[] = BSNetworkService::TCP;
			$host[] = $this->getHost()->getAddress();
			$host[] = $this->getPort();
			$this->name = $host->getContents();
		}
		return $this->name;
	}

	/**
	 * ストリームを開く
	 *
	 * @access public
	 */
	public function open () {
		$error = null;
		$message = null;
		for ($i = 0 ; $i < self::RETRY_LIMIT ; $i ++) {
			if ($this->client = stream_socket_client($this->getName(), $error, $message)) {
				stream_set_timeout($this->client, 10);
				return;
			}
			$this->client = null;
			sleep(1);
		}
		throw new BSNetException('%sに接続出来ません。(%s)', $this, $message);
	}

	/**
	 * ストリームを閉じる
	 *
	 * @access public
	 */
	public function close () {
		if ($this->isOpened()) {
			fclose($this->client);
		}
		$this->client = null;
	}

	/**
	 * ストリームに1行書き込む
	 *
	 * @access public
	 * @param string $str 書き込む内容
	 */
	public function putLine ($str = '') {
		if (!$this->isOpened()) {
			$this->open();
		}
		fwrite($this->client, $str . self::LINE_SEPARATOR);
	}

	/**
	 * ストリームから1行読み込む
	 *
	 * @access public
	 * @param integer $length 一度に読み込む最大のサイズ
	 * @return string 読み込んだ内容
	 */
	public function getLine ($length = self::LINE_BUFFER) {
		if (!$this->isOpened()) {
			$this->open();
		} else if ($this->isEof()) {
			return '';
		}
		$this->line = rtrim(fread($this->client, $length));
		return $this->line;
	}

	/**
	 * 直前のレスポンスを返す
	 *
	 * @access public
	 * @return string 直前のレスポンス
	 */
	public function getPrevLine () {
		return $this->line;
	}

	/**
	 * ストリームの終端まで読んで返す
	 *
	 * @access public
	 * @return string[] 読み込んだ内容
	 */
	public function getLines () {
		return explode(self::LINE_SEPARATOR, stream_get_contents($this->client));
	}

	/**
	 * 開かれているか？
	 *
	 * @access public
	 * @return boolean 開かれていたらtrue
	 */
	public function isOpened () {
		return is_resource($this->client);
	}

	/**
	 * ポインタがEOFに達しているか？
	 *
	 * @access public
	 * @return boolean EOFに達していたらtrue
	 */
	public function isEof () {
		return feof($this->client);
	}

	/**
	 * ホストを返す
	 *
	 * @access public
	 * @return BSHost ホスト
	 */
	public function getHost () {
		return $this->host;
	}

	/**
	 * ホストを設定
	 *
	 * @access public
	 * @param mixed $host ホスト
	 */
	public function setHost ($host) {
		if (($host instanceof BSHost) == false) {
			$host = new BSHost($host);
		}
		$this->host = $host;
		$this->name = null;
	}

	/**
	 * ポート番号を返す
	 *
	 * @access public
	 * @return integer port
	 */
	public function getPort () {
		return $this->port;
	}

	/**
	 * ポート番号を設定
	 *
	 * @access public
	 * @param integer $port port
	 */
	public function setPort ($port = null) {
		if (BSString::isBlank($port)) {
			if (BSString::isBlank($port = $this->getDefaultPort())) {
				throw new BSNetException('ポートが未定義です。');
			}
		}
		$this->port = $port;
		$this->name = null;
	}

	/**
	 * 規定のポート番号を返す
	 *
	 * @access public
	 * @return integer port
	 */
	public function getDefaultPort () {
		return null;
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('TCP/IPソケット "%s"', $this->getName());
	}
}

/* vim:set tabstop=4: */
