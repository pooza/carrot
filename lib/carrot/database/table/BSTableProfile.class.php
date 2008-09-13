<?php
/**
 * @package org.carrot-framework
 * @subpackage database.table
 */

/**
 * テーブルのプロフィール
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSTableProfile {
	protected $database;
	protected $fields = array();
	protected $constraints = array();
	private $name;

	/**
	 * @access public
	 * @param string $table テーブル名
	 */
	public function __construct ($table, BSDatabase $database = null) {
		if (!$database) {
			$database = BSDatabase::getInstance();
		}
		$this->database = $database;
		$this->name = $table;

		if (!in_array($this->getName(), $this->getDatabase()->getTableNames())) {
			throw new BSDatabaseException('%sが取得出来ません。', $this);
		}
	}

	/**
	 * テーブル名を返す
	 *
	 * @access public
	 * @return string テーブル名
	 */
	public function getName () {
		return $this->name;
	}

	/**
	 * データベースを返す
	 *
	 * @access public
	 * @return BSDatabase データベース
	 */
	public function getDatabase () {
		return $this->database;
	}

	/**
	 * テーブルのフィールドリストを配列で返す
	 *
	 * @access public
	 * @return string[][] フィールドのリスト
	 * @abstract
	 */
	abstract public function getFields ();

	/**
	 * テーブルの制約リストを配列で返す
	 *
	 * @access public
	 * @return string[][] 制約のリスト
	 * @abstract
	 */
	abstract public function getConstraints ();

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('テーブルのプロフィール "%s"', $this->getName());
	}
}

/* vim:set tabstop=4 ai: */
?>