<?php
/**
 * Detailアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage DevelopTableReport
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class DetailAction extends BSAction {
	private $tableProfile;

	/**
	 * テーブルプロフィールを返す
	 *
	 * @access public
	 * @return BSTableProfile テーブルプロフィール
	 */
	private function getTableProfile () {
		if (!$this->tableProfile) {
			$this->tableProfile = $this->database->getTableProfile(
				$this->request->getParameter('table')
			);
		}
		return $this->tableProfile;
	}

	public function execute () {
		$this->request->setAttribute('tablename', $this->getTableProfile()->getName());
		$this->request->setAttribute('attributes', $this->getTableProfile()->getAttributes());
		$this->request->setAttribute('fields', $this->getTableProfile()->getFields());
		$this->request->setAttribute('keys', $this->getTableProfile()->getKeys());
		return View::SUCCESS;
	}

	public function validate () {
		return ($this->getTableProfile() != null);
	}

	public function getRequestMethods () {
		return Request::GET;
	}
}

/* vim:set tabstop=4 ai: */
?>