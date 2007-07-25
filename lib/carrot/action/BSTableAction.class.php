<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage action
 */

/**
 * 一覧画面用 アクションひな形
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id: BSTableAction.class.php 361 2007-07-15 12:42:45Z pooza $
 * @abstract
 */
abstract class BSTableAction extends BSAction {
	protected $criteria;
	protected $order;
	protected $rows = array();
	private $isShowRows = false;

	/**
	 * アクションを初期化する
	 *
	 * @access public
	 * @param Context $context Mojaviコンテキスト
	 */
	public function initialize ($context) {
		parent::initialize($context);
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
	protected function getTable () {
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
	 * カレントレコードIDをクリアする
	 *
	 * @access private
	 */
	private function clearRecordID () {
		$this->user->removeAttribute($this->getRecordClassName() . 'ID');
	}

	/**
	 * 検索条件をセッションにキャッシュする
	 *
	 * @access private
	 */
	private function cacheCriteria () {
		$params = $this->request->getParameters();
		unset($params[BSController::MODULE_ACCESSOR]);
		unset($params[BSController::ACTION_ACCESSOR]);
		$name = $this->context->getModuleName() . 'Criteria';
		if ($params) {
			$this->user->setAttribute($name, $params);
		}
		if (!$criteria = $this->user->getAttribute($name)) {
			$criteria = $this->getDefaultCriteria();
			$this->user->setAttribute($name, $criteria);
		}
		$this->request->setParameters($criteria);
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
	 * リストを表示するか
	 *
	 * @access protected
	 * @return boolean 表示して良いならTrue
	 */
	protected function isShowRows () {
		$this->getCriteria();
		return $this->isShowRows;
	}

	/**
	 * リスト表示フラグを設定する
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