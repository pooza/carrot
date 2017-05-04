<?php
/**
 * @package jp.co.b-shock.carrot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSGooglePlusAccountTest extends BSTest {
	public function execute () {
		$this->assert('__construct', $account = new BSGooglePlusAccount('113474433041552257864'));
		$this->assert('getLabel', $account->getLabel() == '秋元 康');
		$this->assert('getName', $account->getName() == '秋元康');
		$this->assert('getURL', $account->getURL()->getContents() == 'https://plus.google.com/113474433041552257864');
		$this->assert('getImageFile', $account->getImageFile('icon') instanceof BSImageFile);
		$this->assert('getActivities', $account->getActivities()->count());
	}
}

/* vim:set tabstop=4: */
