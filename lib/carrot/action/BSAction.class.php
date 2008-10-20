<?php
/**
 * @package org.carrot-framework
 * @subpackage action
 */

/**
 * アクション
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSAction implements BSHTTPRedirector {
	private $attributes;
	private $module;
	private $views;

	/**
	 * @access public
	 * @param BSModule $module 呼び出し元モジュール
	 */
	public function __construct (BSModule $module) {
		$this->module = $module;

		if (!preg_match('/^(.+)Action$/', get_class($this), $matches)) {
			throw new BSInitializeException(
				'アクションの名前が正しくありません。(%s)',
				get_class($this)
			);
		}
		$this->getAttributes()->setParameter('name', $matches[1]);
	}

	/**
	 * @access public
	 * @param string $name プロパティ名
	 * @return mixed 各種オブジェクト
	 */
	public function __get ($name) {
		switch ($name) {
			case 'controller':
				return BSController::getInstance();
			case 'request':
				return BSRequest::getInstance();
			case 'user':
				return BSUser::getInstance();
			case 'database':
				// $this->getTable()は使わない。
				if ($class = $this->getRecordClassName()) {
					$class .= 'Handler';
					if ($table = new $class) {
						return $table->getDatabase();
					}
				}
				return BSDatabase::getInstance();
			default:
				throw new BSMagicMethodException('仮想プロパティ"%s"は未定義です。', $name);
		}
	}

	/**
	 * 実行
	 *
	 * getRequestMethodsで指定されたメソッドでリクエストされた場合に実行される。
	 *
	 * @access public
	 * @return string ビュー名
	 * @abstract
	 */
	abstract public function execute ();

	/**
	 * 初期化
	 *
	 * Falseを返すと、例外が発生。
	 *
	 * @access public
	 * @return boolean 正常終了ならTrue
	 */
	public function initialize () {
		return true;
	}

	/**
	 * デフォルト時ビュー
	 *
	 * getRequestMethodsに含まれていないメソッドから呼び出されたとき、
	 * executeではなくこちらが実行される。
	 *
	 * @access public
	 * @return string ビュー名
	 */
	public function getDefaultView () {
		return BSView::SUCCESS;
	}

	/**
	 * エラー時処理
	 *
	 * バリデート結果が妥当でなかったときに実行される。
	 *
	 * @access public
	 * @return string ビュー名
	 */
	public function handleError () {
		return BSView::ERROR;
	}

	/**
	 * バリデータ登録
	 *
	 * 動的に登録しなければならないバリデータを、ここで登録。
	 * 動的に登録する必要のないバリデータは、バリデーション定義ファイルに記述。
	 *
	 * @access public
	 */
	public function registerValidators () {
	}

	/**
	 * 論理バリデーション
	 *
	 * registerValidatorsで吸収できない、複雑なバリデーションをここに記述。
	 * registerValidatorsで実現できないか、まずは検討すべき。
	 *
	 * @access public
	 * @return boolean 妥当な入力ならTrue
	 */
	public function validate () {
		return !$this->request->hasErrors();
	}

	/**
	 * 属性値を全て返す
	 *
	 * @access public
	 * @return BSArray 属性値
	 */
	public function getAttributes () {
		if (!$this->attributes) {
			$this->attributes = new BSArray;
		}
		return $this->attributes;
	}

	/**
	 * アクション名を返す
	 *
	 * @access public
	 * @return string アクション名
	 */
	public function getName () {
		return $this->getAttributes()->getParameter('name');
	}

	/**
	 * モジュールを返す
	 *
	 * @access public
	 * @return BSModule モジュール
	 */
	public function getModule () {
		return $this->module;
	}

	/**
	 * ビューを返す
	 *
	 * @access public
	 * @param string $name ビュー名
	 * @return BSView ビュー
	 */
	public function getView ($name) {
		$name = BSString::stripControlCharacters($name);
		$class = $this->getName() . $name . 'View';
		if (!$dir = $this->getModule()->getDirectory('views')) {
			throw new BSFileException('%sにビューディレクトリがありません。', $this->getModule());
		} else if (!$file = $dir->getEntry($class . '.class.php')) {
			throw new BSFileException('%sに "%s" がありません。', $this->getModule(), $class);
		}

		if (!$this->views) {
			$this->views = new BSArray;
		}
		if (!$this->views[$name]) {
			require($file->getPath());
			if (!class_exists($class)) {
				throw new BSInitializationException(
					'%sに "%s" が見つかりません。', $this->getModule(), $class
				);
			}
			$this->views[$name] = new $class($this);
		}
		return $this->views[$name];
	}

	/**
	 * カレントレコードIDを返す
	 *
	 * BSModule::getRecordID()のエイリアス。
	 *
	 * @access public
	 * @return integer カレントレコードID
	 * @final
	 */
	final public function getRecordID () {
		return $this->getModule()->getRecordID();
	}

	/**
	 * カレントレコードIDを設定
	 *
	 * BSModule::setRecordID()のエイリアス。
	 *
	 * @access public
	 * @param integer $id カレントレコードID
	 * @final
	 */
	final public function setRecordID ($id) {
		$this->getModule()->setRecordID($id);
	}

	/**
	 * カレントレコードIDをクリア
	 *
	 * BSModule::clearRecordID()のエイリアス。
	 *
	 * @access public
	 * @final
	 */
	final public function clearRecordID () {
		$this->getModule()->clearRecordID();
	}

	/**
	 * 編集中レコードを返す
	 *
	 * @access public
	 * @return BSRecord 編集中レコード
	 */
	public function getRecord () {
		return null;
	}

	/**
	 * テーブルを返す
	 *
	 * @access public
	 * @return BSTableHandler テーブル
	 */
	public function getTable () {
		return $this->getModule()->getTable();
	}

	/**
	 * レコードクラス名を返す
	 *
	 * BSModule::getRecordClassName()のエイリアス
	 *
	 * @access public
	 * @return string レコードクラス名
	 * @final
	 */
	final public function getRecordClassName () {
		return $this->getModule()->getRecordClassName();
	}

	/**
	 * 必要なクレデンシャルを返す
	 *
	 * モジュール規定のクレデンシャル以外の、動的なクレデンシャルを設定。
	 * 必要がある場合、このメソッドをオーバライドする。
	 *
	 * @access public
	 * @return string 必要なクレデンシャル
	 */
	public function getCredential () {
		return $this->getModule()->getCredential();
	}

	/**
	 * 正規なリクエストとして扱うメソッド
	 *
	 * ここに指定したリクエストではexecuteが、それ以外ではgetDefaultViewが実行される
	 *
	 * @access public
	 * @return integer メソッドのビット列
	 */
	public function getRequestMethods () {
		return BSRequest::GET | BSRequest::POST | BSRequest::PUT | BSRequest::DELETE;
	}

	/**
	 * バリデーション設定ファイルを返す
	 *
	 * @access public
	 * @return BSConfigFile バリデーション設定ファイル
	 */
	public function getValidationFile () {
		return $this->getModule()->getValidationFile($this->getName());
	}

	/**
	 * リダイレクト対象
	 *
	 * @access public
	 * @return BSURL
	 */
	public function getURL () {
		$url = new BSURL;
		$path = sprintf('/%s/%s', $this->getModule()->getName(), $this->getName());
		$url->setAttribute('path', $path);
		return $url;
	}

	/**
	 * リダイレクト
	 *
	 * @access public
	 * @return string ビュー名
	 */
	public function redirect () {
		return BSController::getInstance()->redirect($this->getURL());
	}

	/**
	 * 転送
	 *
	 * @access public
	 * @return string ビュー名
	 */
	public function forward () {
		BSActionStack::getInstance()->register($this);
		if (!$this->initialize()) {
			throw new BSInitializeException(
				'%sの%sが初期化できません。',
				$this->getModule(),
				$this
			);
		}

		$filters = new BSFilterChain;
		$filters->loadGlobal();
		$filters->loadModule($this->getModule());

		$filter = new BSExecutionFilter;
		$filter->initialize();
		$filters->register($filter);
		$filters->execute();
		return BSView::NONE;
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('アクション "%s"', $this->getName());
	}
}

/* vim:set tabstop=4 ai: */
?>