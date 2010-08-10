<?php
/**
 * @package org.carrot-framework
 */

/**
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
class BSUtilityTest extends BSTest {

	/**
	 * 実行
	 *
	 * @access public
	 * @return boolean 成功ならTrue
	 */
	public function execute () {
		$this->assert('isPathAbsolute_1', BSUtility::isPathAbsolute('/etc/hosts'));
		$this->assert('isPathAbsolute_2', !BSUtility::isPathAbsolute('www/.htaccess'));
		$this->assert('isPathAbsolute_3', BSUtility::isPathAbsolute('a:/config.sys'));

		$id1 = BSUtility::getUniqueID();
		$id2 = BSUtility::getUniqueID();
		$this->assert('getUniqueID', $id1 != $id2);

		$this->assert('includeFile_1', !BSUtility::includeFile('spyc'));
		$file = new BSFile(BS_LIB_DIR . '/jsmin.php');
		$this->assert('includeFile_2', !BSUtility::includeFile($file));

		$this->assert('executeMethod_1', BSUtility::executeMethod(
			'BSUtility', 'isPathAbsolute', array('/etc/hosts')
		));
		$this->assert('executeMethod_2', !BSUtility::executeMethod(
			'BSUtility', 'isPathAbsolute', array('www/.htaccess')
		));
	}
}

/* vim:set tabstop=4: */
