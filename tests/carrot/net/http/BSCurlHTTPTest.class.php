<?php
/**
 * @package org.carrot-framework
 */

/**
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
class BSCurlHTTPTest extends BSTest {

	/**
	 * 実行
	 *
	 * @access public
	 */
	public function execute () {
		$this->assert('__construct', $http = new BSCurlHTTP('www.b-shock.co.jp'));
		try {
			$http->sendGET('/NotFound');
		} catch (BSHTTPException $e) {
		}
		$this->assert('status_404', $e->getResponse()->getStatus() == 404);
		$this->assert('content-length_404', !!$e->getResponse()->getRenderer()->getSize());
	}
}

/* vim:set tabstop=4: */
