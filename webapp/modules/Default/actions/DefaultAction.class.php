<?php
/**
 * Defaultアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id: DefaultAction.class.php 34 2006-05-08 08:35:43Z pooza $
 */
class DefaultAction extends BSAction {
	public function execute () {
		return View::SUCCESS;
	}

	public function handleError () {
		$url = new BSURL(BS_ROOT_URL . 'index.html');
		return $this->controller->redirect($url);
	}

	public function validate () {
		return $this->request->hasParameter('document');
	}
}

/* vim:set tabstop=4 ai: */
?>