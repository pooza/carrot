<?php
/**
 * UpdateTableProfilesActionアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Console
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class UpdateTableProfilesAction extends BSAction {
	public function execute () {
		foreach (BSDatabase::getDatabases() as $name => $params) {
			$db = BSDatabase::getInstance($name);
			foreach ($db->getTableNames() as $table) {
				$table = $db->getTableProfile($table);
				$table->serialize();
			}
		}
		BSLogManager::getInstance()->put('実行しました。', $this);
		return BSView::NONE;
	}
}

