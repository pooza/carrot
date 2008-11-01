<?php
/**
 * Cryptアクション
 *
 * @package org.carrot-framework
 * @subpackage Console
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class CryptAction extends BSAction {
	public function initialize () {
		$this->request->addOption('t');
		$this->request->parse();
		return true;
	}

	public function execute () {
		printf("平文: %s\n", $this->request['t']);
		printf("暗号文: %s\n", BSCrypt::getInstance()->encrypt($this->request['t']));
		return BSView::NONE;
	}

	public function handleError () {
		print "-tオプションが必要。\n";
		return BSView::NONE;
	}

	public function validate () {
		return $this->request['t'] != null;
	}
}

/* vim:set tabstop=4 ai: */
?>