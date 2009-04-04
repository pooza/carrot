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
	private $handle;
	private $host;
	private $port;
	private $line;
	const RETRY_LIMIT = 5;
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
	 * ストリームを開く
	 *
	 * @access public
	 */
	public function open () {
		for ($i = 0 ; $i < self::RETRY_LIMIT ; $i ++) {
			if ($this->handle = fsockopen($this->getHost()->getAddress(), $this->getPort())) {
				stream_set_timeout($this->handle, 10);
				return;
			}
			$this->handle = null;
			sleep(1);
		}
		throw new BSNetException('%sに接続出来ません。', $this);
	}

	/**
	 * ストリームを閉じる
	 *
	 * @access public
	 */
	public function close () {
		if ($this->isOpened()) {
			fclose($this->handle);
		}
		$this->handle = null;
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
		fputs($this->handle, $str . self::LINE_SEPARATOR);
	}

	/**
	 * ストリームから1行読み込む
	 *
	 * @access public
	 * @param integer $length 一度に読み込む最大のサイズ
	 * @return string 読み込んだ内容
	 */
	public function getLine ($length = 4096) {
		if (!$this->isOpened()) {
			$this->open();
		} else if ($this->isEof()) {
			return '';
		}
		$this->line = rtrim(fgets($this->handle, $length));
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
		sleep(1);
		if (BSString::isBlank($contents = stream_get_contents($this->handle))) {
			return array();
		}
		return explode(self::LINE_SEPARATOR, $contents);
	}

	/**
	 * 開かれているか？
	 *
	 * @access public
	 * @return boolean 開かれていたらtrue
	 */
	public function isOpened () {
		return is_resource($this->handle);
	}

	/**
	 * ポインタがEOFに達しているか？
	 *
	 * @access public
	 * @return boolean EOFに達していたらtrue
	 */
	public function isEof () {
		return feof($this->handle);
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
	}

	/**
	 * 規定のポート番号を返す
	 *
	 * @access public
	 * @return integer port
	 * @static
	 */
	static public function getDefaultPort () {
		return null;
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf(
			'TCP/IPソケット "%s:%d"',
			$this->getHost()->getName(),
			$this->getPort()
		);
	}
}

/* vim:set tabstop=4: */
