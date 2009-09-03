<?php
/**
 * DeniedUserAgentErrorビュー
 *
 * @package org.carrot-framework
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class DeniedUserAgentErrorView extends BSSmartyView {
	public function execute () {
		$this->setTemplate('DeniedUserAgent');
		$this->setStatus(400);
	}
}

/* vim:set tabstop=4: */
