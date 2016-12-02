<?php
/**
 * Pingアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class PingAction extends BSAction {
	public function execute () {
		try {
			BSDatabase::getInstance();
			return BSView::SUCCESS;
		} catch (Exception $e) {
			$this->request->setError('database', $e->getMessage());
			return BSView::ERROR;
		}
	}

	protected function getViewClass () {
		return 'BSJSONView';
	}
}

/* vim:set tabstop=4: */
