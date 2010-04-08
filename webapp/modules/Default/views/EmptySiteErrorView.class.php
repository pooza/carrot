<?php
/**
 * EmptySiteErrorビュー
 *
 * @package org.carrot-framework
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class EmptySiteErrorView extends BSSmartyView {
	public function execute () {
		$this->setStatus(404);
	}
}

/* vim:set tabstop=4: */
