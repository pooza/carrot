<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mail.sender
 */

/**
 * sendmailコマンドによるメール送信機能
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSSendmailMailSender extends BSMailSender {

	/**
	 * 初期化
	 *
	 * @access public
	 * @return string 利用可能ならTrue
	 */
	public function initialize () {
		try {
			self::getSendmailCommand();
			return true;
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
		$sendmail = self::getSendmailCommand();
		$sendmail->addValue('-f');
		$sendmail->addValue($mail->getHeader('from')->getEntity()->getContents());

		if (BS_DEBUG) {
			$to = BSAdministratorRole::getInstance()->getMailAddress();
			$sendmail->addValue($to->getContents());
		} else {
			$sendmail->addValue('-t');
		}

		$command = new BSCommandLine('cat');
		$command->addValue($mail->getFile()->getPath());
		$command->registerPipe($sendmail);
		$command->setBackground(true);
		$command->execute();

		$this->putLog($mail);
	}

	/**
	 * sendmailコマンドを返す
	 * 
	 * @access public
	 * @return BSCommandLine sendmailコマンド
	 * @static
	 */
	static public function getSendmailCommand () {
		$command = new BSCommandLine('sbin/sendmail');
		$command->setDirectory(BSFileUtility::getDirectory('sendmail'));
		$command->addValue('-i');
		return $command;
	}
}

/* vim:set tabstop=4: */
