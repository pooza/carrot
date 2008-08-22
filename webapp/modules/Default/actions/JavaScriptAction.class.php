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
	private $jsset;

	/**
	 * JavaScriptセットを返す
	 *
	 * @access private
	 * @return BSJavaScriptSet JavaScriptセット
	 */
	private function getJavaScriptSet () {
		if (!$this->jsset) {
			if (!$this->request['jsset']) {
				$this->request['jsset'] = 'carrot';
			}
			$this->jsset = new BSJavaScriptSet($this->request['jsset']);
		}
		return $this->jsset;
	}

	public function execute () {
		$this->request->setAttribute('jsset', $this->getJavaScriptSet());
		return BSView::SUCCESS;
	}

	public function validate () {
		return ($this->getJavaScriptSet() != null);
	}

	public function handleError () {
		return $this->controller->forwardTo($this->controller->getNotFoundAction());
	}
}

/* vim:set tabstop=4 ai: */
?>