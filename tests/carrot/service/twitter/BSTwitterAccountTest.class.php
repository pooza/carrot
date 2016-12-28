<?php
/**
 * @package jp.co.b-shock.carrot
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

			try {
				$response = $account->sendDirectMessage(
					$message,
					new BSTwitterAccount(BS_ADMIN_TWITTER)
				);
				$this->assert('sendDirectMessage', $response instanceof BSJSONRenderer);
			} catch (Exception $e) {
			}
		}
	}
}

/* vim:set tabstop=4: */
