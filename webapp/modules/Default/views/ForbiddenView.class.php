<?php
/**
 * Forbiddenビュー
 *
 * @package org.carrot-framework
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class ForbiddenView extends BSSmartyView {
	public function execute () {
		$this->setStatus(403);
	}
}

/* vim:set tabstop=4: */
