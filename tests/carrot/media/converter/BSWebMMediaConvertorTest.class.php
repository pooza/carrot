<?php
/**
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSWebMMediaConvertorTest extends BSTest {
	public function execute () {
		$convertor = new BSWebMMediaConvertor;
		if ($file = BSFileUtility::getDirectory('sample')->getEntry('sample.mov')) {
			$source = $file->copyTo(BSFileUtility::getDirectory('tmp'), 'BSQuickTimeMovieFile');
			$dest = $convertor->execute($source);
			$this->assert('analyzeType', ($dest->analyzeType() == 'video/webm'));
			$source->delete();
			$dest->delete();
		}
	}
}

/* vim:set tabstop=4: */
