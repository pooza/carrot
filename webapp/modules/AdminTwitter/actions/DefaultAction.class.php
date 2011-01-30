<?php
/**
 * Defaultアクション
 *
 * @package org.carrot-framework
 * @subpackage AdminTwitter
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class DefaultAction extends BSAction {
	public function execute () {
		return $this->getModule()->getAction('Summary')->forward();
	}
}

/* vim:set tabstop=4: */
