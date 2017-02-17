<?php
/**
 * @package jp.co.b-shock.carrot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSImagemagick7ImageTest extends BSTest {
	public function execute () {
		$command = new BSCommandLine('bin/convert');
		$command->setDirectory(BSFileUtility::getDirectory('image_magick'));
		if ($command->isExists()) {
			$this->assert('__construct', $image = new BSImagemagick7Image);
			$image->setImage(imagecreatetruecolor(320, 240));
			$this->assert('setType', !$image->setType('image/jpeg'));
			$this->assert('getGDHandle', is_resource($image->getGDHandle()));
			$this->assert('resize', !$image->resize(16, 16));
			$this->assert('getWidth', $image->getWidth() == 16);
			$this->assert('getHeight', $image->getHeight() == 16);
			$this->assert('getType', $image->getType() == 'image/jpeg');
		}
	}
}

/* vim:set tabstop=4: */
