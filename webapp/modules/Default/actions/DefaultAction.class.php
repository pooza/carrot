<?php
/**
 * Defaultアクション
 *
 * @package org.carrot-framework
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class DefaultAction extends BSAction {
	public function execute () {
		return BSView::SUCCESS;
	}

	public function handleError () {
		$url = BSURL::create();
		$url['path'] = BS_HOME_HREF;
		return $url->redirect();
	}

	public function validate () {
		return $this->request->hasParameter('document');
	}
}

/* vim:set tabstop=4: */
