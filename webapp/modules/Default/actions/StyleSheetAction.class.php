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
		$this->request->setAttribute('styleset', $this->getStyleSet());
		return BSView::SUCCESS;
	}

	public function validate () {
		if (!$this->request->hasParameter('styleset')) {
			$this->request->setParameter('styleset', 'carrot');
		}
		return ($this->getStyleSet() != null);
	}

	public function handleError () {
		return $this->controller->forwardTo($this->controller->getNotFoundAction());
	}
}

/* vim:set tabstop=4 ai: */
?>