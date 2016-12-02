<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage database.table
 */

/**
 * テーブルのプロフィール
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @abstract
 */
abstract class BSTableProfile implements BSAssignable, BSSerializable {
	protected $database;
	protected $fields;
	protected $constraints;
	protected $digest;
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

		if (!$this->isExists()) {
			throw new BSDatabaseException($this . 'が取得できません。');
		}

		if (!$this->getSerialized()) {
			$this->serialize();
		}
		$profile = $this->getSerialized();
		$this->fields = new BSArray($profile['fields']);
		$this->constraints = new BSArray($profile['constraints']);
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
	 * テーブルは存在するか？
	 *
	 * @access public
	 * @return boolean 存在するならTrue
	 */
	public function isExists () {
		return $this->getDatabase()->getTableNames()->isContain($this->getName());
	}

	/**
	 * テーブルのフィールドリストを配列で返す
	 *
	 * @access public
	 * @return BSArray フィールドのリスト
	 * @abstract
	 */
	abstract public function getFields ();

	/**
	 * テーブルの制約リストを配列で返す
	 *
	 * @access public
	 * @return BSArray 制約のリスト
	 * @abstract
	 */
	abstract public function getConstraints ();

	/**
	 * テーブルクラスの継承を返す
	 *
	 * @access protected
	 * @return BSArray テーブルクラスの継承
	 */
	protected function getTableClasses () {
		try {
			$name = BSLoader::getInstance()->getClass(
				$this->getName(),
				BSTableHandler::CLASS_SUFFIX
			);
			return new BSArray(BSLoader::getParentClasses($name));
		} catch (Exception $e) {
			return new BSArray;
		}
	}

	/**
	 * レコードクラスの継承を返す
	 *
	 * @access protected
	 * @return BSArray レコードクラスの継承
	 */
	protected function getRecordClasses () {
		try {
			$name = BSLoader::getInstance()->getClass($this->getName());
			return new BSArray(BSLoader::getParentClasses($name));
		} catch (Exception $e) {
			return new BSArray;
		}
	}

	/**
	 * ダイジェストを返す
	 *
	 * @access public
	 * @return string ダイジェスト
	 */
	public function digest () {
		if (!$this->digest) {
			$this->digest = BSCrypt::digest([
				get_class($this),
				$this->getName(),
			]);
		}
		return $this->digest;
	}

	/**
	 * シリアライズ
	 *
	 * @access public
	 */
	public function serialize () {
		$values = [
			'fields' => $this->getFields(),
			'constraints' => $this->getConstraints(),
		];
		BSController::getInstance()->setAttribute($this, $values);
	}

	/**
	 * アサインすべき値を返す
	 *
	 * @access public
	 * @return mixed アサインすべき値
	 */
	public function getAssignableValues () {
		$values = [
			'name' => $this->getName(),
			'name_ja' => BSTranslateManager::getInstance()->execute($this->getName(), 'ja'),
			'table_classes' => $this->getTableClasses(),
			'record_classes' => $this->getRecordClasses(),
			'constraints' => $this->getConstraints(),
		];

		$pattern = '^(' . $this->getDatabase()->getTableNames()->join('|') . ')_id$';
		foreach ($this->getFields() as $field) {
			if (isset($field['is_nullable'])) {
				$field['is_nullable'] = ($field['is_nullable'] == 'YES');
			}
			if (mb_ereg($pattern, $field['column_name'], $matches)) {
				$field['extrenal_table'] = $matches[1];
			}
			$values['fields'][] = $field;
		}

		return $values;
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
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('テーブルプロフィール "%s"', $this->getName());
	}
}

/* vim:set tabstop=4: */
