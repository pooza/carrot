<?php
/**
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSJapaneseHolidayListServiceTest extends BSTest {
	public function execute () {
		$service = new BSJapaneseHolidayListService;
		$service->setDate(BSDate::create('20000101'));
		$this->assert('offsetGet_1', ($service[1] == '元日'));
		$this->assert('offsetGet_2', ($service[10] == '成人の日'));
	}
}

/* vim:set tabstop=4: */
