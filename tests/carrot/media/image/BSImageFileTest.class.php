<?php
/**
 * @package org.carrot-framework
 */

/**
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
class BSImageFileTest extends BSTest {

	/**
	 * 実行
	 *
	 * @access public
	 */
	public function execute () {
		$dir = BSFileUtility::getDirectory('root');
		$src = $dir->getEntry('www/carrotlib/images/button/pictogram.gif', 'BSImageFile');
		$dest = BSFileUtility::getTemporaryFile('ico');
		$dest->setContents($src->getContents());
		$this->assert('__construct', $dest = new BSImageFile($dest->getPath(), 'BSImagickImage'));
		$this->assert('getType', $dest->getType() == 'image/gif');
		$this->assert('setType', $dest->setType('image/vnd.microsoft.icon'));
		$this->assert('setType', $dest->getRenderer()->setType('image/vnd.microsoft.icon'));
p($dest);

		$dest->delete();
	}
}

/* vim:set tabstop=4: */
