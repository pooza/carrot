<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net
 */

/**
 * ソケット
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSSocket.class.php 323 2007-05-15 11:51:34Z pooza $
 */
class BSSocket {
	private $handle;
	private $host;
	private $port;
	const RETRY_LIMIT = 5;
	const LINE_SEPARATOR = "\r\n";

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param BSHost $host ホスト
	 * @param integer $port ポート
	 */
	public function __construct (BSHost $host, $port = null) {
		$this->setHost($host);
		$this->setPort($port);
	}

	/**
	 * デストラクタ
	 *
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
				if (BSController::getInstance()->isDebugMode()) {
					BSLog::put($this . 'に接続しました。');
				}
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
			if (BSController::getInstance()->isDebugMode()) {
				BSLog::put($this . 'から切断しました。');
			}
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
		return rtrim(fgets($this->handle, $length));
	}

	/**
	 * ストリームの終端まで読んで返す
	 *
	 * @access public
	 * @return string[] 読み込んだ内容
	 */
	public function getLines () {
		sleep(1);
		$lines = array();
		do {
			$lines[] = $this->getLine();
			$status = socket_get_status($this->handle);
		} while ($status['unread_bytes']);

		if (!$lines[0] && (count($lines) == 1)) {
			return array();
		}
		return $lines;
	}

	/**
	 * 開かれているか
	 *
	 * @access public
	 * @return boolean 開かれていたらtrue
	 */
	public function isOpened () {
		return is_resource($this->handle);
	}

	/**
	 * ポインタがEOFに達しているか
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
	 * ホストを設定する
	 *
	 * @access public
	 * @param BSHost $host ホスト
	 */
	public function setHost (BSHost $host) {
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
	 * ポート番号を設定する
	 *
	 * @access public
	 * @param integer $port port
	 */
	public function setPort ($port = null) {
		if (!$port) {
			if (!$port = $this->getDefaultPort()) {
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
	public static function getDefaultPort () {
		return null;
	}

	/**
	 * 基本情報を文字列で返す
	 *
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf(
			'ソケット接続 "%s:%d"',
			$this->getHost()->getName(),
			$this->getPort()
		);
	}
}

/* vim:set tabstop=4 ai: */
?>