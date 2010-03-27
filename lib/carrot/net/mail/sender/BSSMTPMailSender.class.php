<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mail.sender
 */

/**
 * SMTPによるメール送信機能
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSSMTPMailSender {
	static private $smtp;

	/**
	 * 初期化
	 *
	 * @access public
	 * @return string 利用可能ならTrue
	 */
	public function initialize () {
		try {
			return (BS_NET_RESOLVABLE  && self::getServer());
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
		$smtp->send();
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
