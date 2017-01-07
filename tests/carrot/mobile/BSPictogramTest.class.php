<?php
/**
 * @package jp.co.b-shock.carrot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSPictogramTest extends BSTest {
	public function execute () {
		$this->assert('getInstance', $picto = BSPictogram::getInstance('晴れ'));
		$this->assert('getID', $picto->getID() == 63647);
		$this->assert('getNumericReference', $picto->getNumericReference() == '&#63647;');
	}
}

/* vim:set tabstop=4: */
