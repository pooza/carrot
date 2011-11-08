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
 */
class BSSocketServer {
	protected $attributes;
	protected $server;
	private $name;
	private $streams;
	const LINE_BUFFER = 4096;

	/**
	 * @access public
	 */
	public function __construct () {
		$this->attributes = new BSArray($this->getSerialized());
	}

	/**
	 * 名前を返す
	 *
	 * @access public
	 * @return string 名前
	 */
	public function getName () {
		if (!$this->name && !BSString::isBlank($port = $this->attributes['port'])) {
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
		if (!(BSRequest::getInstance() instanceof BSConsoleRequest)) {
			$message = new BSStringFormat('%sを開始できません。');
			$message[] = get_class($this);
			throw new BSConsoleException($message);
		}

		$this->open();
		$this->serialize();
		$this->log('開始しました。');
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
		$this->log('終了しました。');
		BSController::getInstance()->removeAttribute($this);
	}

	private function log ($body) {
		$message = new BSStringFormat('%s（ポート:%d, PID:%d）');
		$message[] = $body;
		$message[] = $this->attributes['port'];
		$message[] = $this->attributes['pid'];
		BSLogManager::getInstance()->put($message, $this);
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

	private function open () {
		$port = BSNumeric::getRandom(48557, 49150);
		$this->name = 'tcp://0.0.0.0:' . $port;
		if (!$this->server = stream_socket_server($this->getName())) {
			$message = new BSStringFormat('%sのサーバソケットを作成できません。');
			$message[] = get_class($this);
			throw new BSNetException($message);
		}
		$this->attributes['port'] = $port;
		$this->attributes['pid'] = BSProcess::getCurrentID();
	}

	private function close () {
		if (is_resource($this->server)) {
			foreach ($this->getStreams() as $stream) {
				fclose($stream);
			}
			$this->server = null;
			$this->attributes->clear();
		}
	}

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
		return is_resource($this->server) || BSProcess::isExists($this->attributes['pid']);
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
	 * ダイジェストを返す
	 *
	 * @access public
	 * @return string ダイジェスト
	 */
	public function digest () {
		return BSCrypt::digest(get_class($this));
	}

	/**
	 * シリアライズ
	 *
	 * @access public
	 */
	public function serialize () {
		BSController::getInstance()->setAttribute($this, $this->attributes);
	}

	/**
	 * シリアライズ時の値を返す
	 *
	 * @access public
	 * @return mixed シリアライズ時の値
	 */
	public function getSerialized () {
		return BSController::getInstance()->getAttribute($this);
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
