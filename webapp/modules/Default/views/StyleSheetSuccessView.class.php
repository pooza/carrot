<?php
/**
 * StyleSheetSuccessビュー
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class StyleSheetSuccessView extends BSView {
	public function execute () {
		$this->setEngine($this->request->getAttribute('styleset'));
	}
}

/* vim:set tabstop=4 ai: */
?>