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
abstract class BSRecord
	implements ArrayAccess, BSSerializable, BSAssignable, BSAttachmentContainer {

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

		$message = new BSStringFormat('仮想メソッド"%s"は未定義です。');
		$message[] = $method;
		throw new BadFunctionCallException($message);
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

		$values = new BSArray($values);
		$db = $this->getDatabase();
		$table = $this->getTable();
		$fields = $table->getProfile()->getFields();
		if ($fields[$table->getUpdateDateField()]) {
			$values[$table->getUpdateDateField()] = BSDate::getNow('Y-m-d H:i:s');
		}
		if (!$values->count()) {
			return;
		}

		$db->exec(BSSQL::getUpdateQueryString($table, $values, $this->getCriteria(), $db));
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

		$this->getDatabase()->exec(
			BSSQL::getDeleteQueryString($this->getTable(), $this->getCriteria())
		);
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
	 * 添付ファイルの情報を返す
	 *
	 * @access public
	 * @param string $name 名前
	 * @return string[] 添付ファイルの情報
	 */
	public function getAttachmentInfo ($name = null) {
		if ($file = $this->getAttachment($name)) {
			$info = new BSArray;
			$info['path'] = $file->getPath();
			$info['size'] = $file->getSize();
			$info['type'] = $file->getType();
			$info['filename'] = $this->getAttachmentFileName($name);
			if ($url = $this->getAttachmentURL($name)) {
				$info['url'] = $url->getContents();
			}
			return $info;
		}
	}

	/**
	 * 添付ファイルを返す
	 *
	 * @access public
	 * @param string $name 名前
	 * @return BSFile 添付ファイル
	 */
	public function getAttachment ($name = null) {
		$finder = new BSFileFinder;
		$finder->clearDirectories();
		$finder->registerDirectory($this->getTable()->getDirectory());
		$finder->registerSuffixes(BSMIMEType::getAttachableSuffixes());
		return $finder->execute($this->getAttachmentBaseName($name));
	}

	/**
	 * 添付ファイルを設定
	 *
	 * @access public
	 * @param BSFile $file 添付ファイル
	 * @param string $name 名前
	 */
	public function setAttachment (BSFile $file, $name = null) {
		if ($old = $this->getAttachment($name)) {
			$old->delete();
		}
		$file->setMode(0666);
		$suffix = BSMIMEType::getSuffix($file->analyzeType());
		$file->rename($this->getAttachmentBaseName($name) . $suffix);
		$file->moveTo($this->getTable()->getDirectory());
	}

	/**
	 * 添付ファイルベース名を返す
	 *
	 * @access public
	 * @param string $name 名前
	 * @return string 添付ファイルベース名
	 */
	public function getAttachmentBaseName ($name = null) {
		return sprintf('%06d_%s', $this->getID(), $name);
	}

	/**
	 * 添付ファイルのダウンロード時の名を返す
	 *
	 * @access public
	 * @param string $name 名前
	 * @return string ダウンロード時ファイル名
	 */
	public function getAttachmentFileName ($name = null) {
		if ($file = $this->getAttachment($name)) {
			return $this->getAttachmentBaseName($name) . $file->getSuffix();
		}
	}

	/**
	 * 添付ファイルのURLを返す
	 *
	 * @access public
	 * @param string $name 名前
	 * @return BSURL 添付ファイルURL
	 */
	public function getAttachmentURL ($name = null) {
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
	 * @access public
	 * @param string $key 添え字
	 * @return boolean 要素が存在すればTrue
	 */
	public function offsetExists ($key) {
		return $this->attributes->hasParameter($key);
	}

	/**
	 * @access public
	 * @param string $key 添え字
	 * @return mixed 要素
	 */
	public function offsetGet ($key) {
		return $this->getAttribute($key);
	}

	/**
	 * @access public
	 * @param string $key 添え字
	 * @param mixed 要素
	 */
	public function offsetSet ($key, $value) {
		throw new BSDatabaseException('レコードの属性を直接更新することはできません。');
	}

	/**
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
	 * 属性名へシリアライズ
	 *
	 * @access public
	 * @return string 属性名
	 */
	public function serializeName () {
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
		if ($date = $this->getUpdateDate()) {
			return BSController::getInstance()->getAttribute($this, $date);
		} else {
			return BSController::getInstance()->getAttribute($this);
		}
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
