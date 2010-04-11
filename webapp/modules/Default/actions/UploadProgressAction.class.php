<?php
/**
 * アップロード進捗アクション
 *
 * @package org.carrot-framework
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class UploadProgressAction extends BSAction {
	public function execute () {
		$renderer = new BSJSONRenderer;
		$renderer->setContents(apc_fetch(BS_UPLOAD_PROGRESS_KEY));
		return $this->request->setAttribute('renderer', $renderer);
	}

	public function validate () {
		return extension_loaded('apc');
	}

	public function handleError () {
		return $this->controller->getAction('not_found')->forward();
	}
}

/* vim:set tabstop=4: */
