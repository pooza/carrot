<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mail.sender
 */

/**
 * メール送信機能
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
interface BSMailSender {

	/**
	 * 初期化
	 *
	 * @access public
	 * @return string 利用可能ならTrue
	 */
	public function initialize ();

	/**
	 * 送信
	 *
	 * @access public
	 * @param BSMail $mail メール
	 */
	public function send (BSMail $mail);
}

/* vim:set tabstop=4: */
