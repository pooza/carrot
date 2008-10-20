<?php
/**
 * Defaultアクション
 *
 * @package org.carrot-framework
 * @subpackage DevelopDocumentor
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class DefaultAction extends BSAction {
	public function execute () {
		return $this->getModule()->getAction('Generate')->forward();
	}
}

/* vim:set tabstop=4 ai: */
?>