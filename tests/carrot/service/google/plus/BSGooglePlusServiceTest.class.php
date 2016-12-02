<?php
/**
 * @package jp.co.b-shock.carrot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSGooglePlusServiceTest extends BSTest {
	public function execute () {
		$this->assert('__construct', $service = new BSGooglePlusService);
		$response = $service->sendGET('/plus/v1/people/113474433041552257864');
		$this->assert('sendGET', $response instanceof BSHTTPResponse);
	}
}

/* vim:set tabstop=4: */
