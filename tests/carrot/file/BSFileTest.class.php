<?php
/**
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSFileTest extends BSTest {
	public function execute () {
		$dir = BSFileUtility::getDirectory('sample');

		$file = $dir->getEntry('dirty.csv');
		$this->assert('analyzeType_csv', $file->analyzeType() == 'text/csv');

		$file = $dir->getEntry('spam.eml');
		$this->assert('analyzeType_eml', $file->analyzeType() == 'message/rfc822');

		$file = $dir->getEntry('sample.mov');
		$this->assert('analyzeType_mov', $file->analyzeType() == 'video/quicktime');
	}
}

/* vim:set tabstop=4: */
