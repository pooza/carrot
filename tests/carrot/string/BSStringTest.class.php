<?php
/**
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSStringTest extends BSTest {
	public function execute () {
		$string = BSString::convertWrongCharacters('㈱㈲');
		$this->assert('convertWrongCharacters', $string == '(株)(有)');

		$file = BSFileUtility::getDirectory('sample')->getEntry('dirty.csv', 'BSCSVFile');
		$records = BSString::convertWrongCharacters($file->getEngine()->getRecords());
		$this->assert('convertWrongCharacters', $records[0][1] == '(有)');
		$this->assert('convertWrongCharacters', $records[2][0] == '(2)');

		$string = " \r\n   test\n   ";
		$this->assert('trim', BSString::trim($string) == "\ntest\n");
	}
}

/* vim:set tabstop=4: */
