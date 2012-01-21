<?php
/**
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSSendmailMailSenderTest extends BSTest {
	public function execute () {
		$this->assert('__construct', $sender = new BSSendmailMailSender);
		$this->assert('initialize', $sender->initialize());

		$mail = new BSSmartyMail;
		$mail->getRenderer()->setTemplate('BSException.mail');
		$mail->getRenderer()->setAttribute('message', get_class($this));
		$mail->getRenderer()->setAttribute('priority', get_class($this));

		$this->assert('send', !$sender->send($mail));
	}
}

/* vim:set tabstop=4: */
