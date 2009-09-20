<?php
/**
 * StyleSheetアクション
 *
 * @package org.carrot-framework
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class StyleSheetAction extends BSAction {
	private $styleset;

	/**
	 * スタイルセットを返す
	 *
	 * @access private
	 * @return BSStyleSet スタイルセット
	 */
	private function getStyleSet () {
		if (!$this->styleset) {
			$this->styleset = new BSStyleSet($this->request['styleset']);
		}
		return $this->styleset;
	}

	public function execute () {
		$this->request->setAttribute('renderer', $this->getStyleSet());
		return BSView::SUCCESS;
	}

	public function validate () {
		return !!$this->getStyleSet();
	}

	public function handleError () {
		return $this->controller->getNotFoundAction()->forward();
	}
}

/* vim:set tabstop=4: */
