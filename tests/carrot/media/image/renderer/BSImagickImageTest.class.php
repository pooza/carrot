<?php
/**
 * @package org.carrot-framework
 */

/**
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @abstract
 */
class BSImagickImageTest extends BSTest {
	public function execute () {
		$this->assert('__construct', $image = new BSImagickImage());
		$this->assert('getGDHandle', is_resource($image->getGDHandle()));
		$this->assert('resize', !$image->resize(16, 16));
		$this->assert('getWidth', $image->getWidth() == 16);
		$this->assert('getHeight', $image->getHeight() == 16);
		$this->assert('setType', !$image->setType('image/vnd.microsoft.icon'));
		$this->assert('getType', $image->getType() == 'image/vnd.microsoft.icon');
	}
}

/* vim:set tabstop=4: */
