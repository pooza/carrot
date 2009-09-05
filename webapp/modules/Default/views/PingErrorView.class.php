<?php
/**
 * PingErrorビュー
 *
 * @package org.carrot-framework
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class PingErrorView extends BSView {
	public function execute () {
		$this->setStatus(500);
		$this->setRenderer(new BSPlainTextRenderer);
		$this->renderer->setContents($this->request->getErrors()->join("\n"));
	}
}

/* vim:set tabstop=4: */
