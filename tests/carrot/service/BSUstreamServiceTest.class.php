<?php
/**
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSUstreamServiceTest extends BSTest {
	public function execute () {
		$service = new BSUstreamService;
		$this->assert('getChannelInfo', !!$service->getChannelInfo('rkk-radio-raditama-jpn'));
	}
}

/* vim:set tabstop=4: */
