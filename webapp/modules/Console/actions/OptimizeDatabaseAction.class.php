<?php
/**
 * OptimizeDatabaseアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Console
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class OptimizeDatabaseAction extends BSAction {
	public function initialize () {
		$this->request->addOption('d');
		$this->request->parse();
		return true;
	}

	public function execute () {
		if (!$db = $this->request['d']) {
			$db = 'default';
		}
		$db = BSDatabase::getInstance($db);

		$db->optimize();
		$this->controller->putLog(sprintf('%sを最適化しました。', $db), get_class($db));
		return BSView::NONE;
	}
}

/* vim:set tabstop=4 ai: */
?>