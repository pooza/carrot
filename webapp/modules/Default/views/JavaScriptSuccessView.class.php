<?php
/**
 * JavaScriptSuccessビュー
 *
 * @package org.carrot-framework
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class JavaScriptSuccessView extends BSView {
	public function execute () {
		$this->setEngine($this->request->getAttribute('jsset'));
	}
}

/* vim:set tabstop=4 ai: */
?>