<?php
/**
 * @package org.carrot-framework
 * @subpackage controller
 */

/**
 * Carrotアプリケーションコントローラ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @abstract
 */
abstract class BSController {
	protected $host;
	protected $platform;
	protected $headers;
	protected $actions;
	protected $searchDirectories;
	protected $serializeHandler;
	static private $instance;
	const ACTION_REGISTER_LIMIT = 20;
	const COMPLETED = true;

	/**
	 * @access protected
	 */
	protected function __construct () {
		$this->headers = new BSArray;
		$this->actions = new BSArray;
	}

	/**
	 * @access public
	 * @param string $name プロパティ名
	 * @return mixed 各種オブジェクト
	 */
	public function __get ($name) {
		switch ($name) {
			case 'request':
			case 'user':
				return BSUtility::executeMethod($name, 'getInstance');
		}
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSController インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (!self::$instance) {
			if (PHP_SAPI == 'cli') {
				self::$instance = new BSConsoleController;
			} else {
				self::$instance = new BSWebController;
			}
		}
		return self::$instance;
	}

	/**
	 * @access public
	 */
	public function __clone () {
		throw new BadFunctionCallException(__CLASS__ . 'はコピーできません。');
	}

	/**
	 * ディスパッチ
	 *
	 * @access public
	 */
	public function dispatch () {
		if (BSString::isBlank($module = $this->request[BSModule::ACCESSOR])) {
			$module = BS_MODULE_DEFAULT_MODULE;
		}
		if (BSString::isBlank($action = $this->request[BSAction::ACCESSOR])) {
			$action = BS_MODULE_DEFAULT_ACTION;
		}

		try {
			$module = BSModule::getInstance($module);
			$action = $module->getAction($action);
		} catch (Exception $e) {
			$action = $this->getAction('not_found');
		}
		$action->forward();
	}

	/**
	 * サーバホストを返す
	 *
	 * @access public
	 * @return string サーバホスト
	 */
	public function getHost () {
		if (!$this->host) {
			$this->host = new BSHost($this->getAttribute('SERVER_NAME'));
		}
		return $this->host;
	}

	/**
	 * サーバプラットフォームを返す
	 *
	 * @access public
	 * @return string サーバホスト
	 */
	public function getPlatform () {
		if (!$this->platform) {
			$this->platform = BSPlatform::create(PHP_OS);
		}
		return $this->platform;
	}

	/**
	 * BSSerializeHandlerを返す
	 *
	 * @access protected
	 * @return BSSerializeHandler
	 */
	protected function getSerializeHandler () {
		if (!$this->serializeHandler) {
			$this->serializeHandler = new BSSerializeHandler;
		}
		return $this->serializeHandler;
	}

	/**
	 * モジュールを返す
	 *
	 * @access public
	 * @param string $name モジュール名
	 * @return BSModule モジュール
	 */
	public function getModule ($name = null) {
		if (BSString::isBlank($name)) {
			if ($action = $this->getAction()) {
				return $action->getModule();
			}
			$name = $this->request[BSModule::ACCESSOR];
		}
		return BSModule::getInstance($name);
	}

	/**
	 * アクションスタックを返す
	 *
	 * @access public
	 * @return BSArray アクションスタック
	 */
	public function getActionStack () {
		return $this->actions;
	}

	/**
	 * アクションをアクションスタックに加える
	 *
	 * @access public
	 * @param BSAction $action アクション
	 */
	public function registerAction (BSAction $action) {
		if (self::ACTION_REGISTER_LIMIT < $this->getActionStack()->count()) {
			throw new BadFunctionCallException('フォワードが多すぎます。');
		}
		$this->getActionStack()->push($action);
	}

	/**
	 * 特別なアクションを返す
	 *
	 * @access public
	 * @param string $name アクション名
	 * @return BSAction 名前で指定されたアクション、指定なしの場合は呼ばれたアクション
	 */
	public function getAction ($name = null) {
		if (BSString::isBlank($name)) {
			return $this->getActionStack()->getIterator()->getLast();
		}
		if ($module = $this->getModule($this->getAttribute('module_' . $name . '_module'))) {
			return $module->getAction($this->getAttribute('module_' . $name . '_action'));
		}
	}

	/**
	 * 属性を返す
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @param BSDate $date 比較する日付 - この日付より古い属性値は破棄
	 * @return mixed 属性値
	 */
	public function getAttribute ($name, BSDate $date = null) {
		if (!$date && !is_object($name)) {
			$env = new BSArray;
			$env->setParameters($_ENV);
			$env->setParameters($_SERVER);
			$keys = new BSArray;
			$keys[] = $name;
			$keys[] = 'HTTP_' . $name;
			$keys[] = 'HTTP_' . str_replace('-', '_', $name);
			$keys->uniquize();
			foreach ($keys as $key) {
				if (!BSString::isBlank($value = $env[$key])) {
					return $value;
				}
			}

			$constants = new BSConstantHandler;
			if (!BSString::isBlank($value = $constants[$name])) {
				return $value;
			}
		}
		return $this->getSerializeHandler()->getAttribute($name, $date);
	}

	/**
	 * 属性を設定
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @param mixed $value 値
	 */
	public function setAttribute ($name, $value) {
		$this->getSerializeHandler()->setAttribute($name, $value);
	}

	/**
	 * 属性を削除
	 *
	 * @access public
	 * @param string $name 属性の名前
	 */
	public function removeAttribute ($name) {
		$this->getSerializeHandler()->removeAttribute($name);
	}

	/**
	 * 全ての属性を返す
	 *
	 * @access public
	 * @return mixed[] 全ての属性
	 */
	public function getAttributes () {
		return $this->getSerializeHandler()->getAttributes();
	}

	/**
	 * サーバサイドキャッシュが有効か
	 *
	 * @access public
	 * @return boolean 有効ならTrue
	 */
	public function hasServerSideCache () {
		return false;
	}

	/**
	 * 検索対象ディレクトリを返す
	 *
	 * @access public
	 * @return BSArray ディレクトリの配列
	 */
	public function getSearchDirectories () {
		if (!$this->searchDirectories) {
			$this->searchDirectories = new BSArray;
			$this->searchDirectories[] = BSFileUtility::getDirectory('root');
		}
		return $this->searchDirectories;
	}

	/**
	 * レスポンスヘッダを返す
	 *
	 * @access public
	 * @return BSArray レスポンスヘッダの配列
	 */
	public function getHeaders () {
		return $this->headers;
	}

	/**
	 * レスポンスヘッダを設定
	 *
	 * @access public
	 * @param string $name フィールド名
	 * @param string $value フィールド値
	 */
	public function setHeader ($name, $value) {
		$this->headers->setParameter(
			BSString::stripControlCharacters($name),
			BSString::stripControlCharacters($value)
		);
	}

	/**
	 * バージョン番号込みのアプリケーション名を返す
	 *
	 * @access public
	 * @param string $lang 言語
	 * @return string アプリケーション名
	 */
	public function getName ($lang = 'ja') {
		return sprintf(
			'%s %s (Powered by %s %s)',
			$this->getAttribute('app_name_' . $lang),
			BS_APP_VER,
			BS_CARROT_NAME,
			BS_CARROT_VER
		);
	}
}

/* vim:set tabstop=4: */
