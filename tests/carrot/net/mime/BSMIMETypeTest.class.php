<?php
/**
 * @package jp.co.b-shock.carrot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSMIMETypeTest extends BSTest {
	public function execute () {
		$types = BSMIMEType::getInstance();
		$this->assert('count_1', $types->count() == $types->getSuffixes()->count());
		$this->assert('count_0', 0 < $types->count());
		$this->assert('getType_1', BSMIMEType::getType('.txt') == 'text/plain');
		$this->assert('getType_2', BSMIMEType::getType('txt') == 'text/plain');
		$this->assert('getType_3', BSMIMEType::getType('.html') == 'text/html');
		$this->assert('getType_4', BSMIMEType::getType('.htm') == 'application/octet-stream');
		$this->assert('getType_5', BSMIMEType::getType('.htm', null) == null);
		$this->assert('getType_6', BSMIMEType::getType('.ZIP') == 'application/zip');
		$this->assert('getSuffix_1', BSMIMEType::getSuffix('text/plain') == '.txt');
		$this->assert('getSuffix_2', BSMIMEType::getSuffix('application/unknown') == null);
	}
}

/* vim:set tabstop=4: */
