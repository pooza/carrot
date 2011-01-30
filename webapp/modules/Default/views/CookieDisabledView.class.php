<?php
/**
 * CookieDisabledビュー
 *
 * @package org.carrot-framework
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>

 */
class CookieDisabledView extends BSSmartyView {
	public function execute () {
		$this->setStatus(400);
	}
}

/* vim:set tabstop=4: */
