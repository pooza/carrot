<?php
/**
 * @package org.carrot-framework
 */

/**
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @abstract
 */
class BSHTTPTest extends BSTest {
	public function execute () {
		$this->assert('__construct', $http = new BSHTTP('www.b-shock.co.jp'));
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
