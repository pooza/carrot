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
			try {
				$response = $account->tweet($message);
				$this->assert('tweet', $response instanceof BSJSONRenderer);
			} catch (Exception $e) {
			}
		}
	}
}

/* vim:set tabstop=4: */
