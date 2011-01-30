<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mail.sender
 */

/**
 * メール送信機能
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @abstract
 */
abstract class BSMailSender {

	/**
	 * 初期化
	 *
	 * @access public
	 * @return string 利用可能ならTrue
	 */
	abstract public function initialize ();

	/**
	 * 送信
	 *
	 * @access public
	 * @param BSMail $mail メール
	 */
	abstract public function send (BSMail $mail);

	/**
	 * 送信ログを出力する
	 *
	 * @access protected
	 * @param BSMail $mail 対象メール
	 */
	protected function putLog (BSMail $mail) {
		$recipients = new BSArray;
		foreach ($mail->getRecipients() as $email) {
			$recipients[] = $email->getContents();
		}

		$message = new BSStringFormat('%sから%s宛に、メールを送信しました。');
		$message[] = $mail->getHeader('From')->getEntity()->getContents();
		$message[] = $recipients->join(',');

		BSLogManager::getInstance()->put($message, $this);
	}
}

/* vim:set tabstop=4: */
