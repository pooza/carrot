<?php
/**
 * Cryptアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Console
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class CryptAction extends BSAction {
	public function initialize () {
		$this->request->addOption('t');
		$this->request->parse();
		return true;
	}

	public function execute () {
		$crypt = BSCrypt::getInstance();
		$this->request->setAttribute('plain', $this->request['t']);
		$this->request->setAttribute('crypted', $crypt->encrypt($this->request['t']));
		return BSView::SUCCESS;
	}

	public function handleError () {
		return BSView::ERROR;
	}
}

/* vim:set tabstop=4: */
