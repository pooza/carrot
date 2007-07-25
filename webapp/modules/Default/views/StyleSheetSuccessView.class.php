<?php
/**
 * StyleSheetSuccessビュー
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id: StyleSheetSuccessView.class.php 346 2007-06-26 12:02:41Z pooza $
 */
class StyleSheetSuccessView extends BSView {
	public function execute () {
		$this->setEngine(new BSCSS($this->request->getParameter('style')));
	}
}

/* vim:set tabstop=4 ai: */
?>