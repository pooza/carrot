<?php
/**
 * @package org.carrot-framework
 * @subpackage filter
 */

/**
 * HTTPSによるGETを強制するフィルタ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSHTTPSFilter extends BSFilter {
	public function initialize ($parameters = array()) {
		$this['base_url'] = BS_ROOT_URL_HTTPS;
		return parent::initialize($parameters);
	}

	public function execute () {
		if (!BS_DEBUG
			&& !$this->request->isCLI()
			&& !$this->request->isSSL()
			&& ($this->request->getMethod() == BSRequest::GET)) {

			$url = BSURL::getInstance($this['base_url']);
			$url['path'] = $this->controller->getEnvironment('REQUEST_URI');
			$url->redirect();
			exit;
		}
	}
}

/* vim:set tabstop=4: */
