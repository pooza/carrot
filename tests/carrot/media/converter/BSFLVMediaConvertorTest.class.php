<?php
/**
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSFLVMediaConvertorTest extends BSTest {
	public function execute () {
		$convertor = new BSFLVMediaConvertor;
		if ($file = BSFileUtility::getDirectory('sample')->getEntry('sample.mov')) {
			$source = $file->copyTo(BSFileUtility::getDirectory('tmp'), 'BSMovieFile');
			$dest = $convertor->execute($source);
			$this->assert('analyzeType', ($dest->analyzeType() == 'video/x-flv'));
			$source->delete();
			$dest->delete();
		}
	}
}

/* vim:set tabstop=4: */
