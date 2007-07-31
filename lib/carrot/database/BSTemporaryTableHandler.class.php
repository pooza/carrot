<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage database
 */

/**
 * 一時テーブル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 * @abstract
 */
abstract class BSTemporaryTableHandler extends BSTableHandler {
	private $name;

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param string $criteria 抽出条件
	 * @param string $order ソート順
	 */
	public function __construct ($criteria = null, $order = null) {
		parent::__construct($criteria, $order);
		$this->database->createTemporaryTable(
			$this->getName(),
			$this->getFields(),
			sprintf('PRIMARY KEY (%s)', $this->getKeyField())
		);
	}

	/**
	 * デストラクタ
	 *
	 * @access public
	 */
	public function __destruct () {
		$this->database->exec('DROP TABLE ' . $this->getName());
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
	 * テーブル名を返す
	 *
	 * @access public
	 * @return string テーブル名
	 */
	public function getName () {
		if (!$this->name) {
			$name = array(
				strtolower($this->getRecordClassName()),
				BSDate::getNow('YmdHis'),
				BSNumeric::getRandom(),
			);
			$this->name = implode('_', $name);
		}
		return $this->name;
	}

	/**
	 * フィールド定義を返す
	 *
	 * @access protected
	 * @return string[] フィールド定義
	 * @abstract
	 */
	abstract protected function getFields ();

	/**
	 * レコードクラス名を返す
	 *
	 * @access protected
	 * @return string レコードクラス名
	 * @abstract
	 */
	abstract protected function getRecordClassName ();
}

/* vim:set tabstop=4 ai: */
?>