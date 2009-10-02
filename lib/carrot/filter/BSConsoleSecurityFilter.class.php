<?php
/**
 * @package org.carrot-framework
 * @subpackage filter
 */

/**
 * コンソール認証
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSConsoleSecurityFilter extends BSFilter {
	public function execute () {
		return !$this->request->isCLI();
	}
}

/* vim:set tabstop=4: */
