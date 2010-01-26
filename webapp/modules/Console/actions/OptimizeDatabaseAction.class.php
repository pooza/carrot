<?php
/**
 * OptimizeDatabaseアクション
 *
 * @package org.carrot-framework
 * @subpackage Console
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class OptimizeDatabaseAction extends BSAction {
	public function initialize () {
		$this->request->addOption('d');
		$this->request->parse();
		return true;
	}

	public function execute () {
		if (BSString::isBlank($name = $this->request['d'])) {
			$name = 'default';
		}
		BSDatabase::getInstance($name)->optimize();
		return BSView::NONE;
	}
}

/* vim:set tabstop=4: */
