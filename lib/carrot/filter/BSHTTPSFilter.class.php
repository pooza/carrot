<?php
/**
 * @package org.carrot-framework
 * @subpackage filter
 */

/**
 * HTTPSによるGETを強制するフィルタ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSHTTPSFilter extends BSFilter {
	public function execute (BSFilterChain $filters) {
		if (!$this->controller->isDebugMode()
			&& !$this->request->isCLI()
			&& !$this->request->isSSL()
			&& ($this->request->getMethod() == BSRequest::GET)) {

			$url = new BSURL($this->controller->getConstant('ROOT_URL_HTTPS'));
			$url->setAttribute('path', $this->controller->getEnvironment('REQUEST_URI'));
			$this->controller->redirect($url);
			exit;
		}
		$filters->execute();
	}
}

/* vim:set tabstop=4 ai: */
?>