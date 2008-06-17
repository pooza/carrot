<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage database.table
 */

/**
 * テーブルのプロフィール
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 * @abstract
 */
abstract class BSTableProfile {
	protected $attributes = array();
	protected $database;
	protected $fields = array();
	protected $keys = array();
	private $name;

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param string $table テーブル名
	 */
	public function __construct ($table, BSDatabase $database = null) {
		if (preg_match('/^`([a-z0-9_]+)`$/i', $table, $matches)) {
			$this->name = $matches[1];
		} else {
			$this->name = $table;
		}

		if (!$database) {
			$database = BSDatabase::getInstance();
		}
		$this->database = $database;

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
	 * 全ての属性を返す
	 *
	 * @access public
	 * @return string[][] 全ての属性
	 */
	public function getAttributes () {
		if (!$this->attributes) {
			$this->attributes = array(
				'dsn' => $this->getDatabase()->getDSN(),
				'name' => $this->getName(),
			);
		}
		return $this->attributes;
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
	 * テーブルのキーリストを配列で返す
	 *
	 * @access public
	 * @return string[][] キーのリスト
	 * @abstract
	 */
	abstract public function getKeys ();

	/**
	 * 基本情報を文字列で返す
	 *
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('テーブルのプロフィール "%s"', $this->getName());
	}
}

/* vim:set tabstop=4 ai: */
?>