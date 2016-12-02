<?php
/**
 * @package jp.co.b-shock.carrot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSMIMEDocumentTest extends BSTest {
	public function execute () {
		$mime = new BSMIMEDocument;
		$file = BSFileUtility::getDirectory('sample')->getEntry('spam.eml');
		$mime->setContents($file->getContents());
		$this->assert('getDate', $mime->getDate()->format('Ymd') == '20110605');
	}
}

/* vim:set tabstop=4: */
