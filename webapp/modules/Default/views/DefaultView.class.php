<?php
/**
 * Defaultビュー
 *
 * @package org.carrot-framework
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>

 */
class DefaultView extends BSSmartyView {
	public function execute () {
		$this->setTemplate($this->request['document']);
	}
}

/* vim:set tabstop=4: */
