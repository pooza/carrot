<?php
/**
 * JavaScriptアクション
 *
 * @package org.carrot-framework
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class JavaScriptAction extends BSAction {

	/**
	 * JavaScriptセットを返す
	 *
	 * @access private
	 * @return BSJavaScriptSet JavaScriptセット
	 */
	private function getJavaScriptSet () {
		if (BSString::isBlank($this->request['jsset'])) {
			$this->request['jsset'] = 'carrot';
		}
		return BSJavaScriptSet::getInstance($this->request['jsset']);
	}

	public function execute () {
		$this->request->setAttribute('renderer', $this->getJavaScriptSet());
		return BSView::SUCCESS;
	}

	public function validate () {
		return ($this->getJavaScriptSet() != null);
	}

	public function handleError () {
		return $this->controller->getNotFoundAction()->forward();
	}
}

/* vim:set tabstop=4: */
