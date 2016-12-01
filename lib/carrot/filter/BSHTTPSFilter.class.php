<?php
/**
 * @package org.carrot-framework
 * @subpackage filter
 */

/**
 * HTTPSによるGETを強制するフィルタ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSHTTPSFilter extends BSFilter {
	public function initialize ($params = []) {
		$this['base_url'] = BS_ROOT_URL_HTTPS;
		return parent::initialize($params);
	}

	public function execute () {
		if (!BS_DEBUG
			&& !$this->request->isSSL()
			&& !($this->request instanceof BSConsoleRequest)
			&& ($this->request->getMethod() == 'GET')) {

			$url = BSURL::create($this['base_url']);
			$url['path'] = $this->controller->getAttribute('REQUEST_URI');
			$url->redirect();
			return BSController::COMPLETED;
		}
	}
}

/* vim:set tabstop=4: */
