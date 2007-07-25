<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage filter
 */

/**
 * ホスト認証
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSHostSecurityFilter.class.php 323 2007-05-15 11:51:34Z pooza $
 */
class BSHostSecurityFilter extends BSFilter {

	public function execute ($filters) {
		if (!$this->isAuthenticated()) {
			$e = new BSNetException('リモートアクセス禁止のホストです。');
			$e->sendNotify();
			$this->controller->forward(MO_SECURE_MODULE, MO_SECURE_ACTION);
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
			if (BSController::getInstance()->getClientHost()->isInNetwork($network)) {
				return true;
			}
		}
		return false;
	}
}

/* vim:set tabstop=4 ai: */
?>