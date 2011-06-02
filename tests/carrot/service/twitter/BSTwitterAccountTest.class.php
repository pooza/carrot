<?php
/**
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSTwitterAccountTest extends BSTest {
	public function execute () {
		if (!BSString::isBlank(BS_AUTHOR_TWITTER)) {
			$account = new BSTwitterAccount(BS_AUTHOR_TWITTER);
			$message = BSDate::getNow('YmdHis') . ' ' . $this->controller->getName();
			$this->assert('tweet', $account->tweet($message) instanceof BSJSONRenderer);
		}
	}
}

/* vim:set tabstop=4: */
