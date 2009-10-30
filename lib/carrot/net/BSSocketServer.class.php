<?php
/**
 * @package org.carrot-framework
 * @subpackage net
 */

/**
 * 簡易サーバ
 *
 * onReadを適宜オーバライドして使用すること。
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSSocketServer {
	protected $attributes;
	protected $server;
	private $name;
	private $streams;
	const LINE_BUFFER = 4096;
	const RETRY_LIMIT = 10;

	/**
	 * @access public
	 */
	public function __construct () {
		$this->attributes = new BSArray(BSController::getInstance()->getAttribute($this));
	}

	/**
	 * 名前を返す
	 *
	 * @access public
	 * @return string 名前
	 */
	public function getName () {
		if (!$this->name && !BSString::isBlank($port = $this->getAttribute('port'))) {
			$this->name = 'tcp://0.0.0.0:' . $port;
		}
		return $this->name;
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
		BSLogManager::getInstance()->put($message, $this);

		$this->execute();
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
		BSLogManager::getInstance()->put($message, $this);

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
			$this->name = 'tcp://0.0.0.0:' . $port;
			if ($this->server = stream_socket_server($this->getName())) {
				return $port;
			}
		}

		$message = new BSStringFormat('%sのサーバソケットを作成できません。');
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
			foreach ($this->getStreams() as $stream) {
				fclose($stream);
			}
			$this->server = null;
		}
	}

	/**
	 * イベントループを実行
	 *
	 * @access private
	 */
	private function execute () {
		set_time_limit(0);
		$dummy = array(); //stream_selectに渡すダミー配列
		while ($this->server) {
			$streams = $this->getStreams();
			stream_select($streams, $dummy, $dummy, 500000);
			foreach ($streams as $stream) {
				if ($stream === $this->server) {
					$this->streams[] = stream_socket_accept($this->server);
					continue;
				}
				if (!$this->onRead(rtrim(fread($stream, self::LINE_BUFFER)))) {
					fclose($stream);
				}
			}
		}
	}

	/**
	 * ストリームの配列を返す
	 *
	 * stream_selectに渡す為の配列。
	 * 最初の要素はサーバソケット、以降はクライアントソケット。
	 *
	 * @access private
	 * @return resource[] ストリームの配列
	 */
	private function getStreams () {
		if (!$this->streams) {
			$this->streams = array($this->server);
		}
		foreach ($this->streams as $index => $stream) {
			if (!is_resource($stream)) {
				unset($this->streams[$index]);
			}
		}
		return $this->streams;
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
	 * @return クライアントとの通信を継続するならTrue
	 */
	public function onRead ($line) {
		switch (BSString::toUpper($line)) {
			case 'QUIT':
			case 'EXIT':
				return false;
			case 'RESTART':
				$this->restart();
				return false;
			case 'STOP':
			case 'SHUTDOWN':
				$this->stop();
				return false;
		}
		return true;
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		if (BSString::isBlank($this->getName())) {
			return get_class($this);
		}
		return sprintf('%s "%s"', get_class($this), $this->getName());
	}
}

/* vim:set tabstop=4: */
