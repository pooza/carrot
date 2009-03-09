<?php
/**
 * @package org.carrot-framework
 * @subpackage log.logger
 */

/**
 * メール送信ロガー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSMailLogger extends BSLogger {
	private $server;
	private $patterns;

	/**
	 * 初期化
	 *
	 * @access public
	 * @return string 利用可能ならTrue
	 */
	public function initialize () {
		try {
			$this->server = new BSSmartySender;
			return true;
		} catch (BSNetException $e) {
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
			if ($exception instanceof BSMailException) {
				return;
			}
			foreach ($this->getPatterns() as $pattern) {
				if ($exception instanceof $pattern) {
					return $this->send($exception->getMessage(), $exception->getName());
				}
			}
		} else {
			if ($this->getPatterns()->isIncluded($priority)) {
				return $this->send($message, $priority);
			}
		}
	}

	/**
	 * 送信
	 *
	 * @access private
	 * @param string $message ログメッセージ
	 * @param string $priority 優先順位
	 */
	private function send ($message, $priority) {
		$this->server->setTemplate('BSException.mail');
		$this->server->setAttribute('priority', $priority);
		$this->server->setAttribute('message', $message);
		$this->server->send();
	}

	/**
	 * 対象パターン
	 *
	 * @access private
	 * @return BSArray クラス名の配列
	 */
	private function getPatterns () {
		if (!$this->patterns) {
			$this->patterns = BSString::explode(',', BS_LOG_MAIL_PATTERNS);
		}
		return $this->patterns;
	}
}

/* vim:set tabstop=4: */
