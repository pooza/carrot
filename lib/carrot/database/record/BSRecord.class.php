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
abstract class BSRecord implements ArrayAccess, BSAssignable {
	private $attributes;
	private $table;
	private $criteria;
	private $records;

	/**
	 * @access public
	 * @param BSTableHandler $table テーブルハンドラ
	 * @param string[] $attributes 属性の連想配列
	 */
	public function __construct (BSTableHandler $table, $attributes) {
		$this->table = $table;
		$this->attributes = new BSArray;
		$this->records = new BSArray;
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
			if (!$this->records->hasParameter($name)) {
				$table = BSTableHandler::getInstance($name);
				$this->records[$name] = $table->getRecord($this[$table->getName() . '_id']);
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
		return $this->attributes[strtolower($name)];
	}

	/**
	 * 全属性を返す
	 *
	 * @access public
	 * @return BSArray 全属性値
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
	 * @return BSArray 全属性値
	 * @final
	 */
	final public function getContents () {
		return $this->getAttributes();
	}

	/**
	 * 属性をまとめて設定
	 *
	 * @access public
	 * @param string[] $attributes 属性の連想配列
	 */
	public function setAttributes ($attributes) {
		$this->attributes->setAttributes($attributes);
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
			$message = new BSStringFormat('%sを更新しました。');
			$message[] = $this;
			$this->getTable()->getDatabase()->putLog($message);
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

		$query = BSSQL::getDeleteQueryString($this->getTable()->getName(), $this->getCriteria());
		$this->getTable()->getDatabase()->exec($query);

		if ($flag & BSDatabase::WITH_LOGGING) {
			$message = new BSStringFormat('%sを削除しました。');
			$message[] = $this;
			$this->getTable()->getDatabase()->putLog($message);
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
		foreach (array('name', 'label', 'title') as $name) {
			foreach (array('', '_' . $language) as $suffix) {
				$name .= $suffix;
				if (!BSString::isBlank($label = $this->getAttribute($name))) {
					return $label;
				}
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
	 * 要素が存在するか？
	 *
	 * @access public
	 * @param string $key 添え字
	 * @return boolean 要素が存在すればTrue
	 */
	public function offsetExists ($key) {
		return $this->attributes->hasParameter($key);
	}

	/**
	 * 要素を返す
	 *
	 * @access public
	 * @param string $key 添え字
	 * @return mixed 要素
	 */
	public function offsetGet ($key) {
		return $this->getAttribute($key);
	}

	/**
	 * 要素を設定
	 *
	 * @access public
	 * @param string $key 添え字
	 * @param mixed 要素
	 */
	public function offsetSet ($key, $value) {
		throw new BSDatabaseException('レコードの属性を直接更新することはできません。');
	}

	/**
	 * 要素を削除
	 *
	 * @access public
	 * @param string $key 添え字
	 */
	public function offsetUnset ($key) {
		throw new BSDatabaseException('レコードの属性は削除できません。');
	}

	/**
	 * アサインすべき値を返す
	 *
	 * @access public
	 * @return mixed アサインすべき値
	 */
	public function getAssignValue () {
		return $this->getAttributes();
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		try {
			$word = BSTranslateManager::getInstance()->execute($this->getTable()->getName());
		} catch (BSTranslateException $e) {
			$word = $this->getTable()->getName();
		}
		return sprintf('%s(%s)', $word, $this->getID());
	}
}

/* vim:set tabstop=4: */
