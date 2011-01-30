<?php
/**
 * @package org.carrot-framework
 */

/**
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @abstract
 */
class BSImageTest extends BSTest {
	public function execute () {
		$this->assert('getTypes', BSImage::getTypes()->isContain('image/jpeg'));
		$this->assert('getSuffixes', BSImage::getSuffixes()->isContain('.gif'));

		$dir = BSFileUtility::getDirectory('root');
		$src = $dir->getEntry('www/carrotlib/images/button/pictogram.gif', 'BSImageFile');
		$dest = BSFileUtility::getTemporaryFile('gif', 'BSImageFile');
		$dest->setContents($src->getContents());
		$this->assert('getType', $dest->getType() == 'image/gif');
		$dest->getRenderer()->resize(57, 57);
		$dest->save();
		$this->assert('getWidth', $dest->getRenderer()->getWidth() == 57);
		$this->assert('getHeight', $dest->getRenderer()->getHeight() == 57);
		$dest->delete();
	}
}

/* vim:set tabstop=4: */
