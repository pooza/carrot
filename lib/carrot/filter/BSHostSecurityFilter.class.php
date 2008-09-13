<?php
/**
 * @package org.carrot-framework
 * @subpackage filter
 */

/**
 * ホスト認証
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSHostSecurityFilter extends BSFilter {
	public function execute (BSFilterChain $filters) {
		if (!$this->isAuthenticated()) {
			$e = new BSNetException('リモートアクセス禁止のホストです。');
			$e->sendAlert();
			$this->controller->forwardTo($this->controller->getSecureAction());
			exit;
		}
		$filters->execute();
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

/* vim:set tabstop=4 ai: */
?>