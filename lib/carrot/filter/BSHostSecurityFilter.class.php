<?php
/**
 * @package org.carrot-framework
 * @subpackage filter
 */

/**
 * ホスト認証
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSHostSecurityFilter extends BSFilter {
	public function execute () {
		if (!$this->isAuthenticated()) {
			$e = new BSNetException('リモートアクセス禁止のホストです。');
			$e->sendAlert();
			$this->controller->getSecureAction()->forward();
			exit;
		}
	}

	/**
	 * クライアントホストによる認証
	 *
	 * @access private
	 * @return 許可されたネットワーク内ならTrue
	 */
	private function isAuthenticated () {
		if (!$networks = BSAdministrator::getAllowedNetworks()) {
			return true;
		}

		foreach ($networks as $network) {
			if ($this->request->getHost()->isInNetwork($network)) {
				return true;
			}
		}
		return false;
	}
}

/* vim:set tabstop=4: */
