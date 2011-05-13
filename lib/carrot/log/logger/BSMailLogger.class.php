<?php
/**
 * @package org.carrot-framework
 * @subpackage log.logger
 */

/**
 * メール送信ロガー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSMailLogger extends BSLogger {
	private $patterns;

	/**
	 * 初期化
	 *
	 * @access public
	 * @return string 利用可能ならTrue
	 */
	public function initialize () {
		return !!BSMail::getSender();
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
			if ($this->getPatterns()->isContain($priority)) {
				return $this->send($message, $priority);
			}
		}
	}

	private function send ($message, $priority) {
		$mail = new BSSmartyMail;
		$mail->getRenderer()->setTemplate('BSException.mail');
		$mail->getRenderer()->setAttribute('message', $message);
		$mail->getRenderer()->setAttribute('priority', $priority);
		$mail->send();
	}

	private function getPatterns () {
		if (!$this->patterns) {
			$this->patterns = BSString::explode(',', BS_LOG_MAIL_PATTERNS);
		}
		return $this->patterns;
	}
}

/* vim:set tabstop=4: */
