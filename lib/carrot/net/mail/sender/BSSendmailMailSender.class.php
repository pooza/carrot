<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mail.sender
 */

/**
 * sendmailコマンドによるメール送信機能
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
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
			$this->createCommand();
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
		$sendmail = $this->createCommand();
		$sendmail->push('-f');
		$sendmail->push($mail->getHeader('from')->getEntity()->getContents());

		if (BS_DEBUG) {
			$to = BSAdministratorRole::getInstance()->getMailAddress();
			$sendmail->push($to->getContents());
		} else {
			$sendmail->push('-t');
		}

		$command = new BSCommandLine('cat');
		$command->push($mail->getFile()->getPath());
		$command->registerPipe($sendmail);
		$command->setBackground(true);
		$command->execute();

		$this->log($mail);
	}

	/**
	 * sendmailコマンドを返す
	 * 
	 * @access protected
	 * @return BSCommandLine sendmailコマンド
	 */
	protected function createCommand () {
		$command = new BSCommandLine('sbin/sendmail');
		$command->setDirectory(BSFileUtility::getDirectory('sendmail'));
		$command->push('-i');
		return $command;
	}
}

/* vim:set tabstop=4: */
