<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage database
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
	protected $fields = array();
	protected $keys = array();
	private $name;

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param string $table テーブル名
	 */
	public function __construct ($table) {
		$this->name = $table;

		if (!in_array($this->getName(), $this->database->getTableNames())) {
			throw new BSDatabaseException('%sが取得出来ません。', $this);
		}
	}

	/**
	 * プロパティ取得のオーバライド
	 *
	 * @access public
	 * @param string $name プロパティ名
	 * @return mixed 各種オブジェクト
	 */
	public function __get ($name) {
		switch ($name) {
			case 'database':
				return BSDatabase::getInstance();
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
	 * @abstract
	 */
	abstract public function getAttributes ();

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