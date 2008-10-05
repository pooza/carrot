<?php
/**
 * @package org.carrot-framework
 * @subpackage database.record
 */

/**
 * テーブルのレコード
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSRecord {
	private $attributes = array();
	private $table;
	private $criteria;
	private $records = array();

	/**
	 * @access public
	 * @param BSTableHandler $table テーブルハンドラ
	 * @param string[] $attributes 属性の連想配列
	 */
	public function __construct (BSTableHandler $table, $attributes) {
		$this->table = $table;
		$this->setAttributes($attributes);
	}

	/**
	 * @access public
	 * @param string $method メソッド名
	 * @param mixed[] $values 引数
	 */
	public function __call ($method, $values) {
		if (preg_match('/^get([A-Z][A-Za-z0-9]+)$/', $method, $matches)) {
			$name = $matches[1];
			if (!isset($this->records[$name])) {
				$class = $name . 'Handler';
				$table = new $class;
				$id = $this->getAttribute($table->getName() . '_id');
				$this->records[$name] = $table->getRecord($id);
			}
			return $this->records[$name];
		} 
		throw new BSMagicMethodException('仮想メソッド"%s"は未定義です。', $method);
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
	 * 内容を返す
	 *
	 * getAttributesのエイリアス
	 *
	 * @access public
	 * @return string[] 全属性値
	 * @final
	 */
	final public function getContents () {
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
		if (is_array($name) || is_object($name)) {
			throw new BSRegisterException('属性名が文字列ではありません。');
		}
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
			$this->criteria = sprintf(
				'%s=%s',
				$this->getTable()->getKeyField(),
				$this->getTable()->getDatabase()->quote($this->getID())
			);
		}
		return $this->criteria;
	}

	/**
	 * 更新
	 *
	 * @access public
	 * @param string[] $values 更新する値
	 * @param integer $flag フラグのビット列
	 */
	public function update ($values, $flag = BSDatabase::WITH_LOGGING) {
		if (!$this->isUpdatable()) {
			throw new BSDatabaseException('%sを更新することは出来ません。', $this);
		}

		$query = BSSQL::getUpdateQueryString(
			$this->getTable()->getName(),
			$values,
			$this->getCriteria(),
			$this->getTable()->getDatabase()
		);
		$this->getTable()->getDatabase()->exec($query);

		if ($flag & BSDatabase::WITH_LOGGING) {
			$this->getTable()->getDatabase()->putLog($this . 'を更新しました。');
		}

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
	 * 更新日付のみ更新
	 *
	 * updateメソッドを適切にオーバーライドする必要あり。
	 *
	 * @access public
	 */
	public function touch () {
		$this->update(array(), null);
	}

	/**
	 * 削除
	 *
	 * @access public
	 * @param integer $flag フラグのビット列
	 */
	public function delete ($flag = BSDatabase::WITH_LOGGING) {
		if (!$this->isDeletable()) {
			throw new BSDatabaseException('%sを削除することは出来ません。', $this);
		}

		$query = BSSQL::getDeleteQueryString(
			$this->getTable()->getName(),
			$this->getCriteria()
		);
		$this->getTable()->getDatabase()->exec($query);

		if ($flag & BSDatabase::WITH_LOGGING) {
			$this->getTable()->getDatabase()->putLog($this . 'を削除しました。');
		}
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
		return $this->getAttribute($this->getTable()->getKeyField());
	}

	/**
	 * 更新日を返す
	 *
	 * @access public
	 * @return BSDate 更新日
	 */
	public function getUpdateDate () {
		return new BSDate($this->getAttribute('update_date'));
	}

	/**
	 * 作成日を返す
	 *
	 * @access public
	 * @return BSDate 作成日
	 */
	public function getCreateDate () {
		return new BSDate($this->getAttribute('create_date'));
	}

	/**
	 * ラベルを返す
	 *
	 * @access public
	 * @param string $language 言語
	 * @return string ラベル
	 */
	public function getLabel ($language = 'ja') {
		$names = array(
			'label_' . $language,
			'label',
			'name_' . $language,
			'name',
		);
		foreach ($names as $name) {
			if ($label = $this->getAttribute($name)) {
				return $label;
			}
		}
	}

	/**
	 * ラベルを返す
	 *
	 * getLabelのエイリアス
	 *
	 * @access public
	 * @param string $language 言語
	 * @return string ラベル
	 * @final
	 */
	final public function getName ($language = 'ja') {
		return $this->getLabel($language);
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
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf(
			'%s(%s)',
			BSTranslateManager::getInstance()->execute($this->getTable()->getName()),
			$this->getID()
		);
	}
}

/* vim:set tabstop=4 ai: */
?>