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

	/**
	 * スタイルセットを返す
	 *
	 * @access private
	 * @return BSStyleSet スタイルセット
	 */
	private function getStyleSet () {
		if (BSString::isBlank($this->request['styleset'])) {
			$this->request['styleset'] = 'carrot';
		}
		return BSStyleSet::getInstance($this->request['styleset']);
	}

	public function execute () {
		$this->request->setAttribute('renderer', $this->getStyleSet());
		return BSView::SUCCESS;
	}

	public function validate () {
		return ($this->getStyleSet() != null);
	}

	public function handleError () {
		return $this->controller->getNotFoundAction()->forward();
	}
}

/* vim:set tabstop=4: */
