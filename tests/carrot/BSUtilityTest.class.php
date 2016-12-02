<?php
/**
 * @package jp.co.b-shock.carrot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSUtilityTest extends BSTest {
	public function execute () {
		$this->assert('isPathAbsolute_1', BSUtility::isPathAbsolute('/etc/hosts'));
		$this->assert('isPathAbsolute_2', !BSUtility::isPathAbsolute('www/.htaccess'));
		$this->assert('getUniqueID', BSUtility::getUniqueID() != BSUtility::getUniqueID());
		$this->assert('includeFile_1', !BSUtility::includeFile('spyc'));
		$this->assert('includeFile_2', !BSUtility::includeFile(
			new BSFile(BS_LIB_DIR . '/jsmin.php')
		));
		$this->assert('executeMethod_1', BSUtility::executeMethod(
			'BSUtility', 'isPathAbsolute', ['/etc/hosts']
		));
		$this->assert('executeMethod_2', !BSUtility::executeMethod(
			'BSUtility', 'isPathAbsolute', ['www/.htaccess']
		));
	}
}

/* vim:set tabstop=4: */
