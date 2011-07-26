<?php
/**
 * @package org.carrot-framework
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSPictogramTest extends BSTest {
	public function execute () {
		$this->assert('getInstance', BSPictogram::getInstance('晴れ')->getID() == 63647);
	}
}

/* vim:set tabstop=4: */
