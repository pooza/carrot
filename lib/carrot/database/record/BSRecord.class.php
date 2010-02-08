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
abstract class BSRecord implements ArrayAccess, BSSerializable, BSAssignable {
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
		$this->attributes->setParameters($attributes);
	}

	/**
	 * @access public
	 * @param string $method メソッド名
	 * @param mixed[] $values 引数
	 */
	public function __call ($method, $values) {
		if (mb_ereg('^get([[:upper:]][[:alnum:]]+)$', $method, $matches)) {
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
		return $this->attributes[BSString::toLower($name)];
	}

	/**
	 * 全属性を返す
	 *
	 * @access public
	 * @return BSArray 全属性値
	 */
	public function getAttributes () {
		return clone $this->attributes;
	}

	/**
	 * 抽出条件を返す
	 *
	 * @access protected
	 * @return BSCriteriaSet 抽出条件
	 */
	protected function getCriteria () {
		if (!$this->criteria) {
			$this->criteria = $this->createCriteriaSet();
			$this->criteria->register($this->getTable()->getKeyField(), $this);
		}
		return $this->criteria;
	}

	/**
	 * 更新
	 *
	 * @access public
	 * @param mixed $values 更新する値
	 * @param integer $flags フラグのビット列
	 *   BSDatabase::WITHOUT_LOGGING ログを残さない
	 *   BSDatabase::WITHOUT_SERIALIZE シリアライズしない
	 */
	public function update ($values, $flags = null) {
		if (!$this->isUpdatable()) {
			throw new BSDatabaseException($this . 'を更新することはできません。');
		}

		$fields = $this->getTable()->getProfile()->getFields();
		$field = $this->getTable()->getUpdateDateField();
		if ($fields[$field]) {
			$values[$field] = BSDate::getNow('Y-m-d H:i:s');
		}

		$query = BSSQL::getUpdateQueryString(
			$this->getTable()->getName(),
			$values,
			$this->getCriteria(),
			$this->getDatabase()
		);
		$this->getDatabase()->exec($query);
		$this->attributes->setParameters($values);
		if ($this->isSerializable() && !($flags & BSDatabase::WITHOUT_SERIALIZE)) {
			$this->clearSerialized();
		}
		if (!($flags & BSDatabase::WITHOUT_LOGGING)) {
			$message = new BSStringFormat('%sを更新しました。');
			$message[] = $this;
			$this->getDatabase()->putLog($message);
		}
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
		$this->update(array(), BSDatabase::WITHOUT_LOGGING);
	}

	/**
	 * 削除
	 *
	 * @access public
	 * @param integer $flags フラグのビット列
	 *   BSDatabase::WITHOUT_LOGGING ログを残さない
	 */
	public function delete ($flags = null) {
		if (!$this->isDeletable()) {
			throw new BSDatabaseException($this . 'を削除することはできません。');
		}

		$query = BSSQL::getDeleteQueryString($this->getTable()->getName(), $this->getCriteria());
		$this->getDatabase()->exec($query);
		$this->clearSerialized();

		if (!($flags & BSDatabase::WITHOUT_LOGGING)) {
			$message = new BSStringFormat('%sを削除しました。');
			$message[] = $this;
			$this->getDatabase()->putLog($message);
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
	 * 表示して良いか？
	 *
	 * @access public
	 * @return boolean 表示して良いならTrue
	 */
	public function isVisible () {
		return ($this['status'] == 'show');
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
	 * データベースを返す
	 *
	 * @access public
	 * @return BSDatabase データベース
	 */
	public function getDatabase () {
		return $this->getTable()->getDatabase();
	}

	/**
	 * 抽出条件を生成して返す
	 *
	 * @access public
	 * @return BSCriteriaSet 抽出条件
	 */
	public function createCriteriaSet () {
		return $this->getDatabase()->createCriteriaSet();
	}

	/**
	 * IDを返す
	 *
	 * @access public
	 * @return integer ID
	 */
	public function getID () {
		return $this[$this->getTable()->getKeyField()];
	}

	/**
	 * 更新日を返す
	 *
	 * @access public
	 * @return BSDate 更新日
	 */
	public function getUpdateDate () {
		return BSDate::getInstance($this[$this->getTable()->getUpdateDateField()]);
	}

	/**
	 * 作成日を返す
	 *
	 * @access public
	 * @return BSDate 作成日
	 */
	public function getCreateDate () {
		return BSDate::getInstance($this[$this->getTable()->getCreateDateField()]);
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
				if (!BSString::isBlank($label = $this[$name])) {
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
	 * シリアライズするか？
	 *
	 * @access public
	 * @return boolean シリアライズするならTrue
	 */
	public function isSerializable () {
		return false;
	}

	/**
	 * シリアライズ時の属性名を返す
	 *
	 * @access public
	 * @return string シリアライズ時の属性名
	 */
	public function getSerializedName () {
		return sprintf('%s.%08d', get_class($this), $this->getID());
	}

	/**
	 * シリアライズ
	 *
	 * @access public
	 */
	public function serialize () {
		if (!$this->isSerializable()) {
			throw new BSDatabaseException($this . 'はシリアライズできません。');
		}
		BSController::getInstance()->setAttribute($this, $this->getFullAttributes());
	}

	/**
	 * シリアライズ時の値を返す
	 *
	 * @access public
	 * @return mixed シリアライズ時の値
	 */
	public function getSerialized () {
		return BSController::getInstance()->getAttribute($this);
	}

	/**
	 * シリアライズを削除
	 *
	 * @access public
	 */
	public function clearSerialized () {
		BSController::getInstance()->removeAttribute($this);
	}

	/**
	 * 全てのファイル属性
	 *
	 * @access protected
	 * @return BSArray ファイル属性の配列
	 */
	protected function getFullAttributes () {
		return $this->getAttributes();
	}

	/**
	 * アサインすべき値を返す
	 *
	 * @access public
	 * @return mixed アサインすべき値
	 */
	public function getAssignValue () {
		if ($this->isSerializable()) {
			if (BSString::isBlank($this->getSerialized())) {
				$this->serialize();
			}
			return $this->getSerialized();
		} else {
			return $this->getFullAttributes();
		}
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
