<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage log.logger
 */

/**
 * Twitter DM送信ロガー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSTwitterLogger extends BSLogger {
	private $account;
	private $patterns;

	/**
	 * 初期化
	 *
	 * @access public
	 * @return string 利用可能ならTrue
	 */
	public function initialize () {
		try {
			$this->account = BSAuthorRole::getInstance()->getTwitterAccount();
		} catch (Exception $e) {
			return false;
		}
		return true;
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
			$format = new BSStringFormat('[%s] [%s] [%s] %s');
			$format[] = $this->getServerHostName();
			$format[] = $priority;
			$format[] = $this->getClientHostName();
			$format[] = $message;
			$sendto = BSAdministratorRole::getInstance()->getTwitterAccount();
			$this->account->sendDM($format->getContents(), $sendto);
		}
	}

	private function getPatterns () {
		if (!$this->patterns) {
			$this->patterns = BSString::explode(',', BS_LOG_TWITTER_PATTERNS);
		}
		return $this->patterns;
	}
}

/* vim:set tabstop=4: */
