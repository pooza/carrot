<?php
/**
 * @package org.carrot-framework
 * @subpackage action
 */

/**
 * 一覧画面用 アクションひな形
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSTableAction extends BSAction {
	protected $criteria;
	protected $order;
	protected $rows = array();
	protected $table;
	protected $page;

	/**
	 * 初期化
	 *
	 * Falseを返すと、例外が発生。
	 *
	 * @access public
	 * @return boolean 正常終了ならTrue
	 */
	public function initialize () {
		parent::initialize();
		$this->getModule()->clearRecordID();

		$params = $this->getDefaultParameters();
		$params->setParameters($this->getModule()->getParameterCache());
		$params->setParameters($this->request->getParameters());

		$this->request->setParameters($params->getParameters());
		$this->getModule()->setParameterCache($params);

		if (method_exists($this->getTable(), 'getStatusOptions')) {
			$this->request->setAttribute('status_options', $this->getTable()->getStatusOptions());
		}

		return true;
	}

	/**
	 * テーブルを返す
	 *
	 * @access public
	 * @return BSTableHandler テーブル
	 */
	public function getTable () {
		if (!$this->table) {
			$this->table = clone $this->getModule()->getTable();
			$this->table->setCriteria($this->getCriteria());
			$this->table->setOrder($this->getOrder());
		}
		return $this->table;
	}

	/**
	 * テーブルの内容を返す
	 *
	 * @access protected
	 * @return string[][] テーブルの内容
	 */
	protected function getRows () {
		if (!$this->isShowable()) {
			return array();
		}

		if (!$this->rows) {
			foreach ($this->getTable() as $record) {
				$this->rows[] = $record->getAttributes();
			}
		}
		return $this->rows;
	}

	/**
	 * デフォルトの検索条件を返す
	 *
	 * @access protected
	 * @return BSArray 検索条件
	 */
	protected function getDefaultParameters () {
		return new BSArray;
	}

	/**
	 * 検索条件を返す
	 *
	 * @access protected
	 * @return string[] 検索条件
	 */
	protected function getCriteria () {
		return array();
	}

	/**
	 * ソート順を返す
	 *
	 * @access protected
	 * @return string[] ソート順
	 */
	protected function getOrder () {
		return array();
	}

	/**
	 * リストを表示するか
	 *
	 * @access protected
	 * @return boolean 表示して良いならTrue
	 */
	protected function isShowable () {
		return !$this->request->hasErrors();
	}
}

/* vim:set tabstop=4: */
