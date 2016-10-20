<?php
/**
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSCurlHTTPTest extends BSTest {
	public function execute () {
		$this->assert('__construct', $http = new BSCurlHTTP('www.b-shock.co.jp', 443));
		try {
			$response = $http->sendGET('/NotFound');
		} catch (BSHTTPException $e) {
			$response = $e->getResponse();
		}
		$this->assert('status_404', $response->getStatus() == 404);
		$this->assert('content-length_404', !!$response->getRenderer()->getSize());
	}
}

/* vim:set tabstop=4: */
