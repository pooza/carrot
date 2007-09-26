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
	private $key = 'id';
	private $criteria;
	private $order;
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
	 * 主キーフィールド名を返す
	 *
	 * @access public
	 * @return string 主キーフィールド名
	 */
	public function getKeyField () {
		return $this->key;
	}

	/**
	 * 主キーフィールドを設定する
	 *
	 * @access public
	 * @param string $key 主キーフィールド
	 */
	public function setKeyField ($key) {
		$this->key = $key;
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
		if (!$this->order) {
			$this->setOrder($this->getKeyField());
		}
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
	 * @param mixed[] $primaryKey 検索条件
	 */
	public function getRecord ($primaryKey) {
		if (!is_array($primaryKey)) {
			$primaryKey = array($this->getKeyField() => $primaryKey);
		}

		if ($this->isExecuted()) {
			foreach ($this->getResult() as $record) {
				$match = true;
				foreach ($primaryKey as $field => $value) {
					if ($record[$field] != $value) {
						$match = false;
					}
				}
				if ($match) {
					$class = $this->getRecordClassName();
					return new $class($this, $record);
				}
			}
		} else {
			$table = clone $this;
			$criteria = array();
			foreach ($primaryKey as $field => $value) {
				$criteria[] = $field . '=' . BSSQL::quote($value);
			}
			$table->setCriteria($criteria);
			if ($table->getRecordCount() == 1) {
				$class = $this->getRecordClassName();
				return new $class($this, $table->result[0]);
			}
		}
	}

	/**
	 * レコード追加
	 *
	 * @access public
	 * @param mixed[] $values 値
	 * @return string レコードの主キー
	 */
	public function createRecord ($values) {
		if (!$this->isInsertable()) {
			throw new BSDatabaseException('%sへのレコード挿入は許可されていません。', $this);
		}

		$query = BSSQL::getInsertQueryString($this->getName(), $values);
		$this->database->exec($query);
		if ($this->isAutoIncrement()) {
			$id = $this->database->lastInsertId();
		} else {
			$id = $values[$this->getKeyField()];
		}

		$this->setExecuted(false);
		BSLog::put(sprintf('%s(%s)を作成しました。', $this->getRecordClassName(), $id));
		return $id;
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
	 * 内容を返す
	 *
	 * getResultのエイリアス
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
			$this->query();
		}
		return $this->result;
	}

	/**
	 * クエリーを送信し直して結果を返す
	 *
	 * @access public
	 * @return string[] 結果の配列
	 */
	public function query () {
		$this->result = $this->database->query($this->getQueryString())->fetchAll();
		$this->setExecuted(true);
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
			$sql = BSSQL::getSelectQueryString(
				$this->getKeyField(),
				$this->getName(),
				$this->getCriteria()
			);
			return $this->database->query($sql)->rowCount();
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
		$table = clone $this;
		$table->setFields($this->getKeyField());
		$table->setCriteria($criteria);
		return (0 < $table->getRecordCount());
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
		$labels = array();
		foreach ($this as $record) {
			$labels[$record->getID()] = $record->getLabel($language);
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
					$this->fieldNames[$key] = $translator->translate($key, $language);
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
			$this->name = BSString::underscorize($this->getRecordClassName());
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