<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage database.table
 */

/**
 * ソート可能なテーブル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSSortableTableHandler extends BSTableHandler {

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param string $criteria 抽出条件
	 * @param string $order ソート順
	 */
	public function __construct ($criteria = null, $order = null) {
		parent::__construct($criteria, $order);

		if (BSController::getInstance()->isDebugMode()) {
			$fields = $this->getDatabase()->getTableProfile($this->getName())->getFields();
			foreach (array('status', 'rank') as $name) {
				if (!isset($fields[$name])) {
					throw new BSDatabaseException(
						'%sテーブルには%sフィールドが必要です。',
						$this->getName(),
						$name
					);
				}
			}
		}
	}

	/**
	 * レコード追加可能か？
	 *
	 * @access protected
	 * @return boolean レコード追加可能ならTrue
	 */
	protected function isInsertable () {
		return true;
	}

	/**
	 * オートインクリメントのテーブルか？
	 *
	 * @access public
	 * @return boolean オートインクリメントならTrue
	 */
	public function isAutoIncrement () {
		return true;
	}

	/**
	 * レコード追加
	 *
	 * @access public
	 * @param mixed[] $values 値
	 */
	public function createRecord ($values) {
		$values['create_date'] = BSDate::getNow('Y/m/d H:i:s');
		$values['update_date'] = BSDate::getNow('Y/m/d H:i:s');
		return parent::createRecord($values);
	}

	/**
	 * ソート順文字列を返す
	 *
	 * @access public
	 * @return string ソート順文字列
	 */
	public function getOrder () {
		return 'status, rank, id';
	}

	/**
	 * 順位をクリアする
	 *
	 * @access public
	 */
	public function clearRanks () {
		if (!$criteria = $this->getCriteria()) {
			$criteria = $this->getKeyField() . ' IS NOT NULL';
		}

		$sql = BSSQL::getUpdateQueryString(
			$this->getName(),
			array('rank' => 0),
			$criteria
		);
		BSDatabase::getInstance()->exec($sql);
	}

	/**
	 * 全ステータスを返す
	 *
	 * @access public
	 * @param mixed[] $values 値
	 * @static
	 */
	public static function getStatusOptions () {
		return BSTranslator::getInstance()->getHash(
			array('show', 'hide')
		);
	}
}

/* vim:set tabstop=4 ai: */
?>