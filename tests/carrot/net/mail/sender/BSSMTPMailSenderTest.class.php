<?php
/**
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSSMTPMailSenderTest extends BSTest {
	public function execute () {
		$this->assert('__construct', $sender = new BSSMTPMailSender);
		if (BSString::isBlank(BS_SMTP_HOST)) {
			$this->assert('initialize', !$sender->initialize());
		} else {
			$this->assert('initialize', $sender->initialize());
			$mail = new BSSmartyMail;
			$mail->getRenderer()->setTemplate('BSException.mail');
			$mail->getRenderer()->setAttribute('message', get_class($this));
			$mail->getRenderer()->setAttribute('priority', get_class($this));
			$this->assert('send', !$sender->send($mail));
		}
	}
}

/* vim:set tabstop=4: */
