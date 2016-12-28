<?php
/**
 * @package jp.co.b-shock.carrot
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
		return !!BSMail::createSender();
	}

	/**
	 * ログを出力
	 *
	 * @access public
	 * @param string $message ログメッセージ
	 * @param string $priority 優先順位
	 */
	public function put ($message, $priority) {
		if ($this->getPatterns()->isContain($priority)) {
			return $this->send($message, $priority);
		}
	}

	private function send ($message, $priority) {
		$mail = new BSSmartyMail;
		$mail->getRenderer()->setTemplate('BSException.mail');
		$mail->getRenderer()->setAttribute(
			'from',
			BSRootRole::getInstance()->getMailAddress()->format()
		);
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
