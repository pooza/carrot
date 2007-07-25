<?php
/**
 * DetailAllアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage DevelopTableReport
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id: DetailAllAction.class.php 159 2006-07-16 08:26:26Z pooza $
 */
class DetailAllAction extends BSAction {
	public function execute () {
		$profiles = array();
		foreach ($this->database->getTableNames() as $table) {
			$profile = $this->database->getTableProfile($table);
			$profiles[] = array(
				'tablename' => $profile->getName(),
				'attributes' => $profile->getAttributes(),
				'fields' => $profile->getFields(),
				'keys' => $profile->getKeys(),
			);
		}
		$this->request->setAttribute('profiles', $profiles);

		return View::SUCCESS;
	}

	public function getRequestMethods () {
		return Request::GET;
	}
}

/* vim:set tabstop=4 ai: */
?>