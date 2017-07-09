<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage filter
 */

/**
 * ホスト認証
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSHostSecurityFilter extends BSFilter {
	private $networks;

	public function execute () {
		try {
			foreach ($this->getNetworks() as $network) {
				if ($network->isContain($this->request->getHost())) {
					return false;
				}
			}
			throw new BSNetException('リモートアクセス禁止のホストです。');
		} catch (BSNetException $e) {
			$this->controller->getAction('secure')->forward();
			return BSController::COMPLETED;
		}
	}

	private function getNetworks () {
		if (!$this->networks) {
			$this->networks = BSArray::create();
			if (BSString::isBlank(BS_ADMIN_NETWORKS)) {
				$this->networks[] = new BSNetwork('0.0.0.0/0');
			} else {
				$this->networks[] = new BSNetwork('127.0.0.1/32');
				foreach (BSString::explode(',', BS_ADMIN_NETWORKS) as $network) {
					$this->networks[] = new BSNetwork($network);
				}
			}
		}
		return $this->networks;
	}
}

