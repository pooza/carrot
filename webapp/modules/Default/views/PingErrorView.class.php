<?php
/**
 * PingErrorビュー
 *
 * @package org.carrot-framework
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class PingErrorView extends BSSmartyView {
	public function execute () {
		$this->setTemplate('DefaultMessage.Console');
		$this->getEngine()->setType(BSMediaType::getType('txt'));
		$this->setAttribute('message', 'NG');
	}
}

/* vim:set tabstop=4: */
