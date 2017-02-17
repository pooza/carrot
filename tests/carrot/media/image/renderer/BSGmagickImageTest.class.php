<?php
/**
 * @package jp.co.b-shock.carrot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSGmagickImageTest extends BSTest {
	public function execute () {
		if (extension_loaded('gmagick')) {
			$this->assert('__construct', $image = new BSGmagickImage);
			$this->assert('getGDHandle', is_resource($image->getGDHandle()));
			$this->assert('resize', !$image->resize(16, 16));
			$this->assert('getWidth', $image->getWidth() == 16);
			$this->assert('getHeight', $image->getHeight() == 16);
			$this->assert('setType', !$image->setType('image/png'));
			$this->assert('getType', $image->getType() == 'image/png');
		}
	}
}

/* vim:set tabstop=4: */
