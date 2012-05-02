<?php
/**
 * @package org.carrot-framework
 * @subpackage action
 */

/**
 * アクション
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @abstract
 */
abstract class BSAction implements BSHTTPRedirector, BSAssignable, BSValidatorContainer {
	protected $name;
	protected $title;
	protected $url;
	protected $config;
	protected $module;
	protected $methods;
	protected $renderResource;
	protected $digest;
	const ACCESSOR = 'a';

	/**
	 * @access public
	 * @param BSModule $module 呼び出し元モジュール
	 */
	public function __construct (BSModule $module) {
		$this->module = $module;
	}

	/**
	 * @access public
	 * @param string $name プロパティ名
	 * @return mixed 各種オブジェクト
	 */
	public function __get ($name) {
		switch ($name) {
			case 'controller':
			case 'request':
			case 'user':
				return BSUtility::executeMethod($name, 'getInstance');
			case 'database':
				if ($table = $this->getModule()->getTable()) {
					return $table->getDatabase();
				}
				return BSDatabase::getInstance();
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
	 * executeメソッドを実行可能か？
	 *
	 * getDefaultViewに遷移すべきかどうかの判定。
	 * HEAD又は未定義メソッドの場合、GETとしてふるまう。
	 *
	 * @access public
	 * @return boolean executeメソッドを実行可能ならTrue
	 */
	public function isExecutable () {
		if (BSString::isBlank($method = $this->request->getMethod()) || ($method == 'HEAD')) {
			$method = 'GET';
		}
		return $this->getRequestMethods()->isContain($method);
	}

	/**
	 * キャッシュできるか？
	 *
	 * BSRenderManagerでレンダリング結果をキャッシュできるか。
	 *
	 * @access public
	 * @return boolean キャッシュできるならTrue
	 */
	public function isCacheable () {
		return false;
	}

	/**
	 * キャッシュされているか？
	 *
	 * BSRenderManagerでレンダリング結果がキャッシュされているか。
	 *
	 * @access public
	 * @return boolean キャッシュされているならTrue
	 */
	public function isCached () {
		return $this->isCacheable() && BSRenderManager::getInstance()->hasCache($this);
	}

	/**
	 * レンダーリソースを返す
	 *
	 * @access public
	 * @return string レンダーリソース
	 */
	public function getRenderResource () {
		if (!$this->renderResource) {
			$resource = new BSArray;
			$resource[] = $this->getModule()->getName();
			$resource[] = $this->getName();
			$this->renderResource = $resource->join('_');
		}
		return $this->renderResource;
	}

	/**
	 * ダイジェストを返す
	 *
	 * @access public
	 * @return string ダイジェスト
	 */
	public function digest () {
		if (!$this->digest) {
			$this->digest = BSCrypt::digest($this->getName());
		}
		return $this->digest;
	}

	/**
	 * 初期化
	 *
	 * Falseを返すと、例外が発生。
	 *
	 * @access public
	 * @return boolean 正常終了ならTrue
	 */
	public function initialize () {
		if ($errors = $this->user->getAttribute('errors')) {
			$this->request->setErrors($errors);
			$this->user->removeAttribute('errors');
		}
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
	 * 設定を返す
	 *
	 * @access public
	 * @param string $name 設定名
	 * @return mixed 設定値
	 */
	public function getConfig ($name) {
		if (!$this->config) {
			$this->config = new BSArray(
				$this->getModule()->getConfig($this->getName(), 'actions')
			);
		}
		return $this->config[$name];
	}

	/**
	 * アクション名を返す
	 *
	 * @access public
	 * @return string アクション名
	 */
	public function getName () {
		if (BSString::isBlank($this->name)) {
			if (!mb_ereg('^(.+)Action$', get_class($this), $matches)) {
				$message = new BSStringFormat('アクション "%s" の名前が正しくありません。');
				$message[] = get_class($this);
				throw new BSModuleException($message);
			}
			$this->name = $matches[1];
		}
		return $this->name;
	}

	/**
	 * タイトルを返す
	 *
	 * @access public
	 * @return string タイトル
	 */
	public function getTitle () {
		if (BSString::isBlank($this->title)) {
			$this->title = $this->getConfig('title');
		}
		return $this->title;
	}

	/**
	 * 属性値を全て返す
	 *
	 * @access public
	 * @return BSArray 属性値
	 */
	public function getAttributes () {
		return new BSArray(array(
			'name' => $this->getName(),
			'title' => $this->getTitle(),
		));
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
		if (BSString::isBlank($name) || ($this->request->getMethod() == 'HEAD')) {
			return new BSEmptyView($this, null);
		}

		$class = $this->getViewClass();
		if ($dir = $this->getModule()->getDirectory('views')) {
			foreach (array($name, null) as $suffix) {
				$basename = $this->getName() . $suffix . 'View';
				if ($file = $dir->getEntry($basename . '.class.php')) {
					require $file->getPath();
					$class = BSLoader::getInstance()->getClass($basename);
					break;
				}
			}
		}
		return new $class($this, $name, $this->request->getAttribute('renderer'));
	}

	/**
	 * ビューのクラス名を返す
	 *
	 * @access protected
	 * @return string クラス名
	 */
	protected function getViewClass () {
		if (BSString::isBlank($class = $this->getConfig('view'))) {
			$class = 'BSSmartyView';
			if ($this->request->hasAttribute('renderer')) {
				$class = 'BSView';
			}
		}
		return $class;
	}

	/**
	 * メモリ上限を返す
	 *
	 * @access public
	 * @return integer メモリ上限(MB)、設定の必要がない場合はNULL
	 */
	public function getMemoryLimit () {
	}

	/**
	 * タイムアウト時間を返す
	 *
	 * @access public
	 * @return integer タイムアウト時間(秒)、設定の必要がない場合はNULL
	 */
	public function getTimeLimit () {
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
	 * @param integer $id カレントレコードID、又はレコード
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
	 * 抽出条件を生成して返す
	 *
	 * @access protected
	 * @return BSCriteriaSet 抽出条件
	 */
	protected function createCriteriaSet () {
		return $this->database->createCriteriaSet();
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
	 * クレデンシャルを持たないユーザーへの処理
	 *
	 * @access public
	 * @return string ビュー名
	 */
	public function deny () {
		return $this->controller->getAction('secure')->forward();
	}

	/**
	 * 正規なリクエストとして扱うメソッド
	 *
	 * ここに指定したリクエストではexecuteが、それ以外ではgetDefaultViewが実行される。
	 * 適宜オーバライド。
	 *
	 * @access public
	 * @return BSArray 許可されたメソッドの配列
	 */
	public function getRequestMethods () {
		if (!$this->methods) {
			$this->methods = new BSArray;
			if ($file = $this->getValidationFile()) {
				$config = new BSArray($file->getResult());
				if ($methods = $config['methods']) {
					$this->methods->merge($config['methods']);
					return $this->methods;
				}
			}
			$this->methods[] = 'GET';
			$this->methods[] = 'POST';
		}
		return $this->methods;
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
		if (!$this->url) {
			$this->url = BSURL::create(null, 'carrot');
			$this->url['action'] = $this;
		}
		return $this->url;
	}

	/**
	 * リダイレクト
	 *
	 * @access public
	 * @return string ビュー名
	 */
	public function redirect () {
		return $this->getURL()->redirect();
	}

	/**
	 * URLをクローンして返す
	 *
	 * @access public
	 * @return BSURL
	 */
	public function createURL () {
		return clone $this->getURL();
	}

	/**
	 * 転送
	 *
	 * @access public
	 * @return string ビュー名
	 */
	public function forward () {
		if (!$this->initialize()) {
			throw new BadFunctionCallException($this . 'が初期化できません。');
		}
		$this->controller->registerAction($this);
		$this->createFilterSet()->execute();
		return BSView::NONE;
	}

	/**
	 * フィルターセットを生成して返す
	 *
	 * @access public
	 * @return BSFilterSet フィルターセット
	 */
	public function createFilterSet () {
		return BSLoader::getInstance()->createObject(BS_FILTERSET_CLASS, 'FilterSet');
	}

	/**
	 * 状態オプションをアサインする
	 *
	 * @access protected
	 * @return string ビュー名
	 */
	protected function assignStatusOptions () {
		if ($table = $this->getModule()->getTable()) {
			$this->request->setAttribute('status_options', $table->getStatusOptions());
		}
	}

	/**
	 * アサインすべき値を返す
	 *
	 * @access public
	 * @return mixed アサインすべき値
	 */
	public function getAssignableValues () {
		return $this->getAttributes();
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('%sのアクション"%s"', $this->getModule(), $this->getName());
	}
}

/* vim:set tabstop=4: */
