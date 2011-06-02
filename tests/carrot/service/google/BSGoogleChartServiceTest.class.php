<?php
/**
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSGoogleChartServiceTest extends BSTest {
	public function execute () {
		$this->assert('__construct', $service = new BSGoogleChartService);

		$url = BSURL::create('http://www.b-shock.co.jp/');
		$this->assert(
			'getQRCodeImageFile',
			($service->getQRCodeImageFile($url->getContents()) instanceof BSImageFile)
		);
	}
}

/* vim:set tabstop=4: */
