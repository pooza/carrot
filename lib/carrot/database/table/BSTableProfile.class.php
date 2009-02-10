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
abstract class BSTableProfile implements BSAssignable {
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

		if (!$this->getDatabase()->getTableNames()->isIncluded($this->getName())) {
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
	 * テーブルクラスの継承を返す
	 *
	 * @access public
	 * @return BSArray テーブルクラスの継承
	 */
	public function getTableClassNames () {
		$classes = new BSArray;

		try {
			$class = new ReflectionClass(BSTableHandler::getClassName($this->getName()));
			do {
				$classes[] = $class->getName();
			} while ($class = $class->getParentClass());
		} catch (Exception $e) {
		}

		return $classes;
	}

	/**
	 * レコードクラスの継承を返す
	 *
	 * @access public
	 * @return BSArray レコードクラスの継承
	 */
	public function getRecordClassNames () {
		$classes = new BSArray;

		try {
			$class = new ReflectionClass(BSString::pascalize($this->getName()));
			do {
				$classes[] = $class->getName();
			} while ($class = $class->getParentClass());
		} catch (Exception $e) {
		}

		return $classes;
	}

	/**
	 * アサインすべき値を返す
	 *
	 * @access public
	 * @return mixed アサインすべき値
	 */
	public function getAssignValue () {
		$values = array(
			'name' => $this->getName(),
			'name_ja' => BSTranslateManager::getInstance()->execute($this->getName(), 'ja'),
			'table_classes' => $this->getTableClassNames(),
			'record_classes' => $this->getRecordClassNames(),
			'constraints' => $this->getConstraints(),
		);

		$pattern = sprintf(
			'/^(%s)_id$/',
			$this->getDatabase()->getTableNames()->join('|')
		);
		foreach ($this->getFields() as $field) {
			if (isset($field['is_nullable'])) {
				$field['is_nullable'] = ($field['is_nullable'] == 'YES');
			}
			if (preg_match($pattern, $field['column_name'], $matches)) {
				$field['extrenal_table'] = $matches[1];
			}
			$values['fields'][] = $field;
		}

		return $values;
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('テーブルのプロフィール "%s"', $this->getName());
	}
}

/* vim:set tabstop=4: */
