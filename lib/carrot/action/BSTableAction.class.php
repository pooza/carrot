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
	private $isShowRows = false;
	private $table;

	/**
	 * アクションを初期化
	 *
	 * @access public
	 */
	public function initialize () {
		parent::initialize();
		$this->clearRecordID();
		$this->cacheCriteria();
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
			$name = $this->getRecordClassName() . 'Handler';
			$this->table = new $name(
				$this->getCriteria(),
				$this->getOrder()
			);
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
		if (!$this->isShowRows()) {
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
	 * カレントレコードIDをクリア
	 *
	 * @access protected
	 */
	protected function clearRecordID () {
		$this->user->removeAttribute($this->getRecordClassName() . 'ID');
	}

	/**
	 * 検索条件をセッションにキャッシュ
	 *
	 * @access protected
	 */
	protected function cacheCriteria () {
		$params = $this->request->getParameters();
		unset($params[BSController::MODULE_ACCESSOR]);
		unset($params[BSController::ACTION_ACCESSOR]);
		unset($params['page']);
		unset($params['order']);
		$name = $this->controller->getModule()->getName() . 'Criteria';
		if (!$criteria = $this->user->getAttribute($name)) {
			$criteria = $this->getDefaultCriteria();
		}
		foreach ($this->request->getParameters() as $key => $value) {
			if ($this->request->hasParameter($key)) {
				$criteria[$key] = $value;
			}
		}
		$this->user->setAttribute($name, $criteria);
		$this->request->setParameters($criteria);
	}

	/**
	 * 検索条件のキャッシュをクリア
	 *
	 * @access protected
	 */
	protected function clearCriteria () {
		$this->user->removeAttribute(
			$this->controller->getModule()->getName() . 'Criteria'
		);
	}

	/**
	 * デフォルトの検索条件を返す
	 *
	 * @access protected
	 * @return string[] 検索条件
	 */
	protected function getDefaultCriteria () {
		return array();
	}

	/**
	 * 検索条件を返す
	 *
	 * @access protected
	 * @return string[] 検索条件
	 */
	protected function getCriteria () {
		return $this->getDefaultCriteria();
	}

	/**
	 * デフォルトのソート順を返す
	 *
	 * @access protected
	 * @return string[] ソート順
	 */
	protected function getDefaultOrder () {
		return array();
	}

	/**
	 * ソート順を返す
	 *
	 * @access protected
	 * @return string[] ソート順
	 */
	protected function getOrder () {
		return $this->getDefaultOrder();
	}

	/**
	 * リストを表示するか？
	 *
	 * @access protected
	 * @return boolean 表示して良いならTrue
	 */
	protected function isShowRows () {
		$this->getCriteria();
		return $this->isShowRows;
	}

	/**
	 * リスト表示フラグを設定
	 *
	 * @access protected
	 * @param boolean $flag 設定値
	 */
	protected function setShowRows ($flag) {
		$this->isShowRows = $flag;
	}
}

/* vim:set tabstop=4 ai: */
?>