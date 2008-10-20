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
			if (!$this->request['styleset']) {
				$this->request['styleset'] = 'carrot';
			}
			$this->styleset = new BSStyleSet($this->request['styleset']);
		}
		return $this->styleset;
	}

	public function execute () {
		$this->request->setAttribute('styleset', $this->getStyleSet());
		return BSView::SUCCESS;
	}

	public function validate () {
		return ($this->getStyleSet() != null);
	}

	public function handleError () {
		return $this->controller->getNotFoundAction()->forward();
	}
}

/* vim:set tabstop=4 ai: */
?>