<?php
/**
 * @package org.carrot-framework
 * @subpackage log.logger
 */

/**
 * XMPPメッセージ送信ロガー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSJabberLogger extends BSLogger {
	private $server;
	private $patterns;

	/**
	 * @access public
	 */
	public function __destruct () {
		if ($this->server) {
			$this->server->disconnect();
		}
	}

	/**
	 * 初期化
	 *
	 * @access public
	 * @return string 利用可能ならTrue
	 */
	public function initialize () {
		if (BS_XMPP_SSL && !extension_loaded('openssl')) {
			return false;
		} else if (BSString::isBlank(BS_APP_XMPP_JID)) {
			return false;
		}

		try {
			$this->server = new BSXMPP;
			return true;
		} catch (Exception $e) {
			$this->server = null;
			return false;
		}
	}

	/**
	 * ログを出力
	 *
	 * @access public
	 * @param mixed $message ログメッセージ又は例外
	 * @param string $priority 優先順位
	 */
	public function put ($message, $priority = self::DEFAULT_PRIORITY) {
		if ($message instanceof BSException) {
			$exception = $message;
			if ($exception instanceof BSXMPPException) {
				return;
			}
			foreach ($this->getPatterns() as $pattern) {
				if ($exception instanceof $pattern) {
					return $this->send($exception->getMessage(), $exception->getName());
				}
			}
		} else {
			if ($this->getPatterns()->isContain($priority)) {
				return $this->send($message, $priority);
			}
		}
	}

	private function send ($message, $priority) {
		$message = [
			'[' . BS_APP_NAME_JA . ']',
			'[' . $_SERVER['REMOTE_ADDR'] . ']',
			'[' . $priority . ']',
			$message,
		];
		return $this->server->sendMessage(implode(' ', $message));
	}

	private function getPatterns () {
		if (!$this->patterns) {
			$this->patterns = BSString::explode(',', BS_LOG_JABBER_PATTERNS);
		}
		return $this->patterns;
	}
}

/* vim:set tabstop=4: */
