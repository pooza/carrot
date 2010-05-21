<?php
/**
 * DeniedUserAgentビュー
 *
 * @package org.carrot-framework
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class DeniedUserAgentView extends BSSmartyView {
	public function execute () {
		$this->setStatus(400);
	}
}

/* vim:set tabstop=4: */
