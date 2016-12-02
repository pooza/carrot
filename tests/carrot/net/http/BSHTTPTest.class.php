<?php
/**
 * @package jp.co.b-shock.carrot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSHTTPTest extends BSTest {
	public function execute () {
		$this->assert('__construct', $http = new BSHTTP('www.b-shock.co.jp'));
		try {
			$response = $http->sendGET('/');
		} catch (BSHTTPException $e) {
			$response = $e->getResponse();
		}
		$this->assert('status_301', $response->getStatus() == 301);
	}
}

/* vim:set tabstop=4: */
