<?php
/**
 * @package jp.co.b-shock.carrot
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
		$this->assert('trim', BSString::trim($string) == "test");

		$string = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:54.0) Gecko/20100101 Firefox/54.0";
		$this->assert('split', BSString::split($string, 38) == "Mozilla/5.0 (Macintosh; Intel Mac OS X\n 10.12; rv:54.0) Gecko/20100101 Firefo\nx/54.0");
		$this->assert('split-flowed', BSString::split($string, 38, true) == "Mozilla/5.0 (Macintosh; Intel Mac OS X \n 10.12; rv:54.0) Gecko/20100101 Firefo \nx/54.0");
	}
}

/* vim:set tabstop=4: */
