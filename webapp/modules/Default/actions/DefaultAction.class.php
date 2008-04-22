<?php
/**
 * Defaultアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class DefaultAction extends BSAction {
	public function execute () {
		return BSView::SUCCESS;
	}

	public function handleError () {
		$url = new BSURL();
		if (defined('APP_HOME_HREF')) {
			$url->setAttribute('path', APP_HOME_HREF);
		} else {
			$url->setAttribute('path', '/index.html');
		}
		return $this->controller->redirect($url);
	}

	public function validate () {
		return $this->request->hasParameter('document');
	}
}

/* vim:set tabstop=4 ai: */
?>