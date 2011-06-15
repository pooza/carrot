<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mail.sender
 */

/**
 * SMTPによるメール送信機能
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSSMTPMailSender extends BSMailSender {
	static private $smtp;

	/**
	 * 初期化
	 *
	 * @access public
	 * @return string 利用可能ならTrue
	 */
	public function initialize () {
		try {
			return (BS_NET_RESOLVABLE && self::getServer());
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * 送信
	 *
	 * @access public
	 * @param BSMail $mail メール
	 */
	public function send (BSMail $mail) {
		$smtp = self::getServer();
		$smtp->setMail($mail);
		$response = $smtp->send();
		$this->log($mail, $response);
	}

	/**
	 * 送信ログを出力する
	 *
	 * @access protected
	 * @param BSMail $mail 対象メール
	 * @param string $response レスポンス行
	 */
	protected function log (BSMail $mail, $response = null) {
		$recipients = new BSArray;
		foreach ($mail->getRecipients() as $email) {
			$recipients[] = $email->getContents();
		}

		$message = new BSStringFormat('%sから%s宛に、メールを送信しました。(%s)');
		$message[] = $mail->getHeader('From')->getEntity()->getContents();
		$message[] = $recipients->join(',');
		$message[] = $response;

		BSLogManager::getInstance()->put($message, $this);
	}

	/**
	 * SMTPサーバを返す
	 * 
	 * @access public
	 * @return BSSMTP SMTPサーバ
	 * @static
	 */
	static public function getServer () {
		if (!self::$smtp) {
			self::$smtp = new BSSMTP;
		}
		return self::$smtp;
	}
}

/* vim:set tabstop=4: */
