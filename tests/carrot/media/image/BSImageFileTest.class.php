<?php
/**
 * @package jp.co.b-shock.carrot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSImageFileTest extends BSTest {
	public function execute () {
		$dir = BSFileUtility::getDirectory('sample');
		$src = $dir->getEntry('uproda258674.jpg', 'BSImageFile');

		if (extension_loaded('imagick')) {
			$dest = BSFileUtility::createTemporaryFile('ico');
			$dest->setContents($src->getContents());
			$this->assert('__construct', $dest = new BSImageFile($dest->getPath(), 'BSImagickImage'));
			$this->assert('setType', !$dest->getRenderer()->setType('image/vnd.microsoft.icon'));
			$this->assert('getType', $dest->getRenderer()->getType() == 'image/vnd.microsoft.icon');
			$dest->getRenderer()->resize(57, 57);
			$this->assert('getWidth', $dest->getRenderer()->getWidth() == 57);
			$this->assert('getHeight', $dest->getRenderer()->getHeight() == 57);
			$dest->save();
			$dest->delete();
		}

		if (extension_loaded('gmagick')) {
			$dest = BSFileUtility::createTemporaryFile('png');
			$dest->setContents($src->getContents());
			$this->assert('__construct', $dest = new BSImageFile($dest->getPath(), 'BSGmagickImage'));
			$this->assert('setType', !$dest->getRenderer()->setType('image/png'));
			$this->assert('getType', $dest->getRenderer()->getType() == 'image/png');
			$dest->getRenderer()->resize(128, 128);
			$this->assert('getWidth', $dest->getRenderer()->getWidth() == 128);
			$this->assert('getHeight', $dest->getRenderer()->getHeight() == 128);
			$dest->save();
			$dest->delete();
		}
	}
}

/* vim:set tabstop=4: */
