<?php
/**
 * StyleSheetアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class StyleSheetAction extends BSAction {
	public function execute () {
		return View::SUCCESS;
	}

	public function validate () {
		if (!$this->request->hasParameter('style')) {
			$this->request->setParameter('style', 'carrot');
		}
		return (BSCSS::getStyleSet($this->request->getParameter('style')) != null);
	}

	public function handleError () {
		return $this->controller->forward(
			BSController::NOT_FOUND_MODULE,
			BSController::NOT_FOUND_ACTION
		);
	}
}

/* vim:set tabstop=4 ai: */
?>