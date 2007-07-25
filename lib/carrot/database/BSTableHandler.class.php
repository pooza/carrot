<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage database
 */

/**
 * データベーステーブル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 * @abstract
 */
abstract class BSTableHandler implements IteratorAggregate {
	private $fields = '*';
	private $criteria;
	private $order = 'id';
	private $page;
	private $pagesize = 20;
	private $executed = false;
	private $result = array();
	private $queryString;
	private $recordClassName;
	private $name;
	private $fieldNames = array();
	const WITH_PAGING = true;
	const WITHOUT_PAGING = false;

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param string $criteria 抽出条件
	 * @param string $order ソート順
	 */
	public function __construct ($criteria = null, $order = null) {
		$this->setCriteria($criteria);
		$this->setOrder($order);
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
	 * 未定義メソッドの呼び出し
	 *
	 * @access public
	 * @param string $method メソッド名
	 * @param mixed[] $values 引数
	 */
	public function __call ($method, $values) {
		switch ($method) {
			case ('get' . $this->getRecordClassName()):
				return $this->getRecord($values[0]);
			case ('create' . $this->getRecordClassName()):
				return $this->createRecord($values[0]);
			default:
				throw new BSDatabaseException('仮想メソッド"%s"は未定義です。', $method);
		}
	}

	/**
	 * 出力フィールド文字列を返す
	 *
	 * @access public
	 * @return string 出力フィールド文字列
	 */
	public function getFields () {
		return $this->fields;
	}

	/**
	 * 出力フィールド文字列を設定
	 *
	 * @access public
	 * @param mixed $fields 配列または文字列による出力フィールド
	 */
	public function setFields ($fields) {
		if ($fields) {
			$this->fields = BSSQL::getFieldsString($fields);
			$this->setExecuted(false);
		}
	}

	/**
	 * 抽出条件文字列を返す
	 *
	 * @access public
	 * @return string 抽出条件文字列
	 */
	public function getCriteria () {
		return $this->criteria;
	}

	/**
	 * 抽出条件文字列を設定
	 *
	 * @access public
	 * @param mixed $criteria 配列または文字列による抽出条件
	 */
	public function setCriteria ($criteria) {
		if ($criteria) {
			$this->criteria = BSSQL::getCriteriaString($criteria);
			$this->setExecuted(false);
		}
	}

	/**
	 * ソート順文字列を返す
	 *
	 * @access public
	 * @return string ソート順文字列
	 */
	public function getOrder () {
		return $this->order;
	}

	/**
	 * ソート順文字列を設定
	 *
	 * @access public
	 * @param mixed $order 配列または文字列によるソート順
	 */
	public function setOrder ($order) {
		if ($order) {
			$this->order = BSSQL::getOrderString($order);
			$this->setExecuted(false);
		}
	}

	/**
	 * ページ番号を返す
	 *
	 * @access public
	 * @return integer ページ番号
	 */
	function getPage () {
		return $this->page;
	}

	/**
	 * ページ番号を設定する
	 *
	 * @access public
	 * @param integer $page ページ番号
	 */
	function setPage ($page = null) {
		if ($this->getLastPage() < $page) {
			$page = $this->getLastPage();
		} else if ($page < 1){
			$page = 1;
		}
		$this->page = $page;
		$this->setExecuted(false);
	}

	/**
	 * ページサイズを返す
	 *
	 * @access public
	 * @return integer ページサイズ
	 */
	function getPageSize () {
		return $this->pagesize;
	}

	/**
	 * ページ番号を設定する
	 *
	 * @access public
	 * @param integer $pagesize ページサイズ
	 */
	function setPageSize ($pagesize) {
		if (1 < $pagesize) {
			$this->pagesize = $pagesize;
			$this->setExecuted(false);
		}
	}

	/**
	 * レコードを返す
	 *
	 * @access public
	 * @param mixed[] $values 検索条件
	 */
	public function getRecord ($values) {
		if (!is_array($values)) {
			$fields = $this->getKeyFields();
			if (count($fields) == 1) {
				$values = array($fields[0] => $values);
			} else {
				throw new BSDatabaseException(
					'キーの値が不適切で、レコードを特定出来ません。'
				);
			}
		}

		if ($this->isExecuted()) {
			foreach ($this->getResult() as $record) {
				$match = true;
				foreach ($values as $key => $value) {
					if (!isset($record[$key])) {
						if (preg_match('/^[^\.]+\.([^\.]+)$/', $key, $matches)) {
							$key = $matches[1];
						}
					}
					if ($record[$key] != $value) {
						$match = false;
					}
				}
				if ($match) {
					$class = $this->getRecordClassName();
					return new $class($this, $record);
				}
			}
		} else {
			$criteria = array();
			foreach ($values as $key => $value) {
				$criteria[] = $key . '=' . BSSQL::quote($value);
			}
			$this->setCriteria($criteria);
			if ($this->getRecordCount() == 1) {
				$class = $this->getRecordClassName();
				return new $class($this, $this->result[0]);
			}
		}
	}

	/**
	 * レコード追加
	 *
	 * @access public
	 * @param mixed[] $values 値
	 */
	public function createRecord ($values) {
		if (!$this->isInsertable()) {
			throw new BSDatabaseException('%sへのレコード挿入は許可されていません。', $this);
		}

		$this->database->exec(BSSQL::getInsertQueryString($this->getName(), $values));
		if ($this->isAutoIncrement()) {
			$fields = $this->getKeyFields();
			$key = array($fields[0] => $this->database->lastInsertId());
		} else {
			$key = array();
			foreach ($this->getKeyFields() as $name) {
				$key[$name] = $values[$name];
			}
		}

		$this->setExecuted(false);
		$message = sprintf(
			'%s(%s)を作成しました。',
			$this->getRecordClassName(),
			BSString::toString($key)
		);
		BSLog::put($message);
		return $key;
	}

	/**
	 * レコード追加可能か？
	 *
	 * @access protected
	 * @return boolean レコード追加可能ならTrue
	 */
	protected function isInsertable () {
		return false;
	}

	/**
	 * クエリーは実行されたか？
	 *
	 * @access protected
	 * @return boolean 実行されたならTrue
	 */
	protected function isExecuted () {
		return $this->executed;
	}

	/**
	 * クエリー実行フラグを設定する
	 *
	 * @access protected
	 * @param boolean $executed クエリー実行フラグ
	 */
	protected function setExecuted ($executed) {
		if (!$this->executed = $executed) {
			$this->queryString = null;
			$this->result = array();
		}
	}

	/**
	 * イテレータを返す
	 *
	 * @access public
	 * @return BSTableIterator イテレータ
	 */
	public function getIterator () {
		return new BSTableIterator($this);
	}

	/**
	 * 内容を返す - getResultへのエイリアス
	 *
	 * @access public
	 * @return string[] 結果の配列
	 */
	public function getContents () {
		return $this->getResult();
	}

	/**
	 * 結果を返す
	 *
	 * @access public
	 * @return string[] 結果の配列
	 */
	public function getResult () {
		if (!$this->isExecuted()) {
			$this->result = $this->database->query($this->getQueryString())->fetchAll();
			$this->setExecuted(true);
		}
		return $this->result;
	}

	/**
	 * レコード数を返す
	 *
	 * @access public
	 * @param boolean $mode ページングされたレコード数を返すか？
	 * @return integer レコード数
	 */
	public function getRecordCount ($mode = self::WITH_PAGING) {
		if ($mode == self::WITHOUT_PAGING) {
			$query = BSSQL::getSelectQueryString(
				$this->getKeyFields(),
				$this->getName(),
				$this->getCriteria()
			);
			$rs = $this->database->query($query);
			return count($rs->fetchAll());
		} else {
			return count($this->getResult());
		}
	}

	/**
	 * クエリー文字列を返す
	 *
	 * @access public
	 * @return string クエリー文字列
	 */
	public function getQueryString () {
		if (!$this->queryString) {
			if ($this->getPage()) {
				$this->queryString = BSSQL::getSelectQueryString(
					$this->getFields(),
					$this->getName(),
					$this->getCriteria(),
					$this->getOrder(),
					null,
					$this->getPage(),
					$this->getPageSize()
				);
			} else {
				$this->queryString = BSSQL::getSelectQueryString(
					$this->getFields(),
					$this->getName(),
					$this->getCriteria(),
					$this->getOrder()
				);
			}
		}
		return $this->queryString;
	}

	/**
	 * レコードは存在するか
	 *
	 * @access public
	 * @param string[] $criteria 検索条件
	 */
	public function isExist ($criteria) {
		$this->setFields($this->getKeyFields());
		$this->setCriteria($criteria);
		return (0 < $this->getRecordCount());
	}

	/**
	 * ページ数を返す
	 *
	 * @access public
	 * @return integer ページ数
	 */
	function getLastPage () {
		if (!$page = ceil($this->getRecordCount(self::WITHOUT_PAGING) / $this->getPageSize())) {
			$page = 1;
		}
		return $page;
	}

	/**
	 * 最終ページか？
	 *
	 * @access public
	 * @return boolean 最終ページならTrue
	 */
	function isLastPage () {
		return $this->getPage() == $this->getLastPage();
	}

	/**
	 * 現在の抽出条件で抽出して、配列で返す
	 *
	 * @access public
	 * @param string $language 言語
	 * @return string[] ラベルの配列
	 */
	public function getLabels ($language = 'ja') {
		$fields = $this->getKeyFields();
		if (count($fields) != 1) {
			throw new BSDatabaseException('主キーの設定が変更されています。');
		}

		$labels = array();
		foreach ($this as $record) {
			$name = $record->getAttribute($fields[0]);
			$labels[$name] = $record->getLabel($language);
		}
		return $labels;
	}

	/**
	 * フィールド名の配列を返す
	 *
	 * @access public
	 * @param string $language 言語
	 * @return string[] フィールド名の配列
	 */
	public function getFieldNames ($language = 'ja') {
		if (!$this->fieldNames) {
			if ($result = $this->getResult()) {
				$translator = BSTranslator::getInstance();
				foreach ($result[0] as $key => $value) {
					$this->fieldNames[] = $translator->translate($key, $language);
				}

				// Excelの誤認識対策
				if (strtolower($this->fieldNames[0]) == 'id') {
					$name = $translator->translate($this->getName(), $language) . 'ID';
					$this->fieldNames[0] = $name;
				}
			}
		}
		return $this->fieldNames;
	}

	/**
	 * テーブル名を返す
	 *
	 * @access public
	 * @return string テーブル名
	 */
	public function getName () {
		if (!$this->name) {
			$name = preg_replace('/[A-Z]/', '_\\0', $this->getRecordClassName());
			$name = preg_replace('/^_/', '', $name);
			$name = strtolower($name);
			$this->name = $name;
		}
		return $this->name;
	}

	/**
	 * レコードクラス名を返す
	 *
	 * @access protected
	 * @return string レコードクラス名
	 */
	protected function getRecordClassName () {
		if (!$this->recordClassName) {
			if (preg_match('/^([A-Za-z]+)Handler$/', get_class($this), $matches)) {
				$this->recordClassName = $matches[1];
			} else {
				throw new BSDatabaseException(
					'"%s"のクラス名が正しくありません。', get_class($this)
				);
			}
		}
		return $this->recordClassName;
	}

	/**
	 * 主キーフィールド名を返す
	 *
	 * @access public
	 * @return string[] 主キーフィールド名
	 */
	public function getKeyFields () {
		return array('id');
	}

	/**
	 * オートインクリメントのテーブルか？
	 *
	 * @access public
	 * @return boolean オートインクリメントならTrue
	 */
	public function isAutoIncrement () {
		return false;
	}

	/**
	 * 基本情報を文字列で返す
	 *
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('テーブル "%s"', $this->getName());
	}
}

/* vim:set tabstop=4 ai: */
?>