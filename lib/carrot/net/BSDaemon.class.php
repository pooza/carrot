<?php
/**
 * @package org.carrot-framework
 * @subpackage net
 */

/**
 * デーモン
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSDaemon {
	protected $attributes;
	protected $server;
	protected $client;
	const LINE_BUFFER = 4096;
	const RETRY_LIMIT = 10;

	/**
	 * @access public
	 */
	public function __construct () {
		$this->attributes = new BSArray(BSController::getInstance()->getAttribute($this));
	}

	/**
	 * 開始
	 *
	 * @access public
	 */
	public function start () {
		if (!BSRequest::getInstance()->isCLI()) {
			$message = new BSStringFormat('%sを開始できません。');
			$message[] = get_class($this);
			throw new BSConsoleException($message);
		}

		$params = new BSArray;
		$params['port'] = $this->open();
		$params['pid'] = BSProcess::getCurrentID();
		BSController::getInstance()->setAttribute($this, $params->getParameters());
		$this->attributes = $params;

		$message = new BSStringFormat('開始しました。（ポート:%d, PID:%d）');
		$message[] = $this->getAttribute('port');
		$message[] = $this->getAttribute('pid');
		BSController::getInstance()->putLog($message, $this);

		$this->executeLoop();
	}

	/**
	 * 停止
	 *
	 * @access public
	 */
	public function stop () {
		if (!$this->isActive()) {
			return;
		}

		$this->close();

		$message = new BSStringFormat('終了しました。（ポート:%d, PID:%d）');
		$message[] = $this->getAttribute('port');
		$message[] = $this->getAttribute('pid');
		BSController::getInstance()->putLog($message, $this);

		BSController::getInstance()->removeAttribute($this);
	}

	/**
	 * 再起動
	 *
	 * @access public
	 */
	public function restart () {
		$this->stop();
		$this->start();
	}

	/**
	 * サーバソケットを開く
	 *
	 * @access private
	 * @return integer ポート番号
	 */
	private function open () {
		for ($i = 0 ; $i < self::RETRY_LIMIT ; $i ++) {
			$port = BSNumeric::getRandom(48557, 49150);
			if ($this->server = stream_socket_server('tcp://0.0.0.0:' . $port)) {
				stream_set_blocking($this->server, true);
				return $port;
			}
		}

		$message = new BSStringFormat('%sのサーバソケット作成に失敗しました。');
		$message[] = get_class($this);
		throw new BSNetException($message);
	}

	/**
	 * サーバソケットを閉じる
	 *
	 * @access private
	 */
	private function close () {
		if (is_resource($this->server)) {
			fclose($this->server);
			$this->server = null;
		}
	}

	/**
	 * イベントループを実行
	 *
	 * @access private
	 */
	private function executeLoop () {
		set_time_limit(0);
		while ($this->server) {
			$this->client = stream_socket_accept($this->server);
			while ($this->client && ($line = fread($this->client, self::LINE_BUFFER))) {
				$this->onRead(rtrim($line));
			}
		}
	}

	/**
	 * クライアントを切断する
	 *
	 * @access private
	 */
	private function disconnectClient () {
		if (is_resource($this->client)) {
			fclose($this->client);
			$this->client = null;
		}
	}

	/**
	 * 属性を全て返す
	 *
	 * @access public
	 * @return BSArray 属性
	 */
	public function getAttributes () {
		return $this->attributes;
	}

	/**
	 * 属性値を返す
	 *
	 * @access public
	 * @param string $name 属性名
	 * @return mixed 属性値
	 */
	public function getAttribute ($name) {
		return $this->attributes[$name];
	}

	/**
	 * 動作中か？
	 *
	 * @access public
	 * @return boolean 動作中ならTrue
	 */
	public function isActive () {
		return is_resource($this->server) || BSProcess::isExists($this->getAttribute('pid'));
	}

	/**
	 * 受信時処理
	 *
	 * @access public
	 * @param string $line 受信文字列
	 */
	public function onRead ($line) {
		switch (strtoupper($line)) {
			case 'QUIT':
			case 'EXIT':
				return $this->disconnectClient();
			case 'RESTART':
				$this->disconnectClient();
				return $this->restart();
			case 'STOP':
			case 'SHUTDOWN':
				$this->disconnectClient();
				return $this->stop();
		}
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('デーモン "%s"', get_class($this));
	}
}

/* vim:set tabstop=4: */
