<?php
/**
 * @package jp.co.b-shock.carrot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSMPEG4MediaConvertorTest extends BSTest {
	public function execute () {
		$convertor = new BSMPEG4MediaConvertor;
		if ($file = BSFileUtility::getDirectory('sample')->getEntry('sample.mov')) {
			$source = $file->copyTo(BSFileUtility::getDirectory('tmp'), 'BSQuickTimeMovieFile');
			$dest = $convertor->execute($source);
			$this->assert('analyzeType', ($dest->analyzeType() == 'video/mp4'));
			$source->delete();
			$dest->delete();
		}
	}
}

/* vim:set tabstop=4: */
