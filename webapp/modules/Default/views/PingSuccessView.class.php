<?php
/**
 * PingSuccessビュー
 *
 * @package org.carrot-framework
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class PingSuccessView extends BSView {
	public function execute () {
		$this->setRenderer(new BSPlainTextRenderer);
		$this->renderer->setContents('OK');
	}
}

/* vim:set tabstop=4: */
