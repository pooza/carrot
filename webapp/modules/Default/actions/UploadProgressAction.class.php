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
		$result = new BSArray(apc_fetch('upload_' . BS_UPLOAD_PROGRESS_KEY));
		if ($result['total'] == $result['current']) {
			$result->clear();
		}
		$renderer = new BSJSONRenderer;
		$renderer->setContents($result);
		$this->request->setAttribute('renderer', $renderer);
		return BSView::SUCCESS;
	}

	public function validate () {
		return extension_loaded('apc');
	}

	public function handleError () {
		return $this->controller->getAction('not_found')->forward();
	}
}

/* vim:set tabstop=4: */