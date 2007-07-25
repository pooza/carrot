<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage database
 */

/**
 * テーブルのレコード
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSRecord.class.php 334 2007-06-08 11:59:26Z pooza $
 * @abstract
 */
abstract class BSRecord {
	private $attributes = array();
	private $table;
	private $criteria;

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param BSTableHandler $table テーブルハンドラ
	 * @param string[] $attributes 属性の連想配列
	 */
	public function __construct (BSTableHandler $table, $attributes) {
		$this->table = $table;
		$this->setAttributes($attributes);
	}

	/**
	 * 未定義メソッドの呼び出し
	 *
	 * @access public
	 * @param string $method メソッド名
	 * @param mixed[] $values 引数
	 */
	public function __call ($method, $values) {
		if (preg_match('/^get([A-Z][A-Za-z0-9]+)$/', $method, $matches)) {
			$class = $matches[1] . 'Handler';

			$table = new $class;
			$id = $this->getAttribute($table->getName() . '_id');
			return $table->getRecord($id);
		} 
		throw new BSDatabaseException('仮想メソッド"%s"は未定義です。', $method);
	}

	/**
	 * 属性を返す
	 *
	 * @access public
	 * @param string $name 属性名
	 * @return string 属性値
	 */
	public function getAttribute ($name) {
		$name = strtolower($name);
		if (isset($this->attributes[$name])) {
			return $this->attributes[$name];
		} else if (preg_match('/^[^\.]+\.([^\.]+)$/', $name, $matches)) {
			// 属性名が table.id 形式の場合
			$name = strtolower($matches[1]);
			if (isset($this->attributes[$name])) {
				return $this->attributes[$name];
			}
		}
	}

	/**
	 * 全属性を返す
	 *
	 * @access public
	 * @return string[] 全属性値
	 */
	public function getAttributes () {
		return $this->attributes;
	}

	/**
	 * 内容を返す - getAttributesへのエイリアス
	 *
	 * @access public
	 * @return string[] 全属性値
	 */
	public function getContents () {
		return $this->getAttributes();
	}

	/**
	 * 属性を設定
	 *
	 * @access public
	 * @param string $name 属性名
	 * @param string $value 属性値
	 */
	public function setAttribute ($name, $value) {
		$this->attributes[$name] = $value;
	}

	/**
	 * 属性をまとめて設定
	 *
	 * @access public
	 * @param string[] $attributes 属性の連想配列
	 */
	public function setAttributes ($attributes) {
		$this->attributes = array_merge($this->attributes, $attributes);
	}

	/**
	 * 抽出条件を返す
	 *
	 * @access public
	 * @return string 抽出条件
	 */
	public function getCriteria () {
		if (!$this->criteria) {
			$criteria = array();
			foreach ($this->getTable()->getKeyFields() as $key) {
				$criteria[] = $key . '=' . BSSQL::quote($this->getAttribute($key));
			}
			$this->criteria = BSSQL::getCriteriaString($criteria);
		}
		return $this->criteria;
	}

	/**
	 * 更新
	 *
	 * @access public
	 * @param string[] $values 更新する値
	 */
	public function update ($values) {
		if (!$this->isUpdatable()) {
			throw new BSDatabaseException('%sを更新することは出来ません。', $this);
		}

		$query = BSSQL::getUpdateQueryString(
			$this->getTable()->getName(),
			$values,
			$this->getCriteria()
		);
		BSDatabase::getInstance()->exec($query);
		BSLog::put($this . 'を更新しました。');

		$this->setAttributes($values);
	}

	/**
	 * 更新可能か？
	 *
	 * @access protected
	 * @return boolean 更新可能ならTrue
	 */
	protected function isUpdatable () {
		return false;
	}

	/**
	 * 削除
	 *
	 * @access public
	 */
	public function delete () {
		if (!$this->isDeletable()) {
			throw new BSDatabaseException('%sを削除することは出来ません。', $this);
		}

		$query = BSSQL::getDeleteQueryString(
			$this->getTable()->getName(),
			$this->getCriteria()
		);
		BSDatabase::getInstance()->exec($query);
		BSLog::put($this . 'を削除しました。');
	}

	/**
	 * 削除可能か？
	 *
	 * @access protected
	 * @return boolean 削除可能ならTrue
	 */
	protected function isDeletable () {
		return false;
	}

	/**
	 * 生成元テーブルハンドラを返す
	 *
	 * @access public
	 * @return BSTableHandler テーブルハンドラ
	 */
	public function getTable () {
		return $this->table;
	}

	/**
	 * IDを返す
	 *
	 * @access public
	 * @return integer ID
	 */
	public function getID () {
		return $this->getAttribute('id');
	}

	/**
	 * ラベルを返す - 適宜オーバーライドすること
	 *
	 * @access public
	 * @param string $language 言語
	 * @return string ラベル
	 */
	public function getLabel ($language = 'ja') {
		return $this->getAttribute('name');
	}

	/**
	 * レコードクラス名を返す
	 *
	 * @access protected
	 * @return string レコードクラス名
	 */
	protected function getRecordClassName () {
		return get_class($this);
	}

	/**
	 * 基本情報を文字列で返す
	 *
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		$values = array();
		foreach ($this->getTable()->getKeyFields() as $key) {
			$values[] = $key . $this->getAttribute($key);
		}
		return sprintf('%s(%s)', $this->getRecordClassName(), implode(',', $values));
	}
}

/* vim:set tabstop=4 ai: */
?>