<?php
/**
 * @package org.carrot-framework
 * @subpackage controller
 */

/**
 * Carrotアプリケーションコントローラ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSController {
	private $host;
	private $headers;
	private $actions;
	const MODULE_ACCESSOR = 'm';
	const ACTION_ACCESSOR = 'a';
	const ACTION_REGISTER_LIMIT = 20;

	/**
	 * @access public
	 * @param string $name プロパティ名
	 * @return mixed 各種オブジェクト
	 */
	public function __get ($name) {
		switch ($name) {
			case 'request':
				return BSRequest::getInstance();
			case 'user':
				return BSUser::getInstance();
			default:
				throw new BSMagicMethodException('仮想プロパティ"%s"は未定義です。', $name);
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
		if (PHP_SAPI == 'cli') {
			return BSConsoleController::getInstance();
		} else {
			return BSWebController::getInstance();
		}
	}

	/**
	 * ディスパッチ
	 *
	 * @access public
	 */
	public function dispatch () {
		if (BSString::isBlank($module = $this->request[self::MODULE_ACCESSOR])) {
			$module = BS_MODULE_DEFAULT_MODULE;
		}
		if (BSString::isBlank($action = $this->request[self::ACTION_ACCESSOR])) {
			$action = BS_MODULE_DEFAULT_ACTION;
		}

		try {
			$module = BSModule::getInstance($module);
			$action = $module->getAction($action);
		} catch (Exception $e) {
			$action = $this->getNotFoundAction();
		}
		$action->forward();
	}

	/**
	 * サーバ環境変数を返す
	 *
	 * @access public
	 * @param string $name サーバ環境変数の名前
	 * @return mixed サーバ環境変数
	 */
	public function getEnvironment ($name) {
		if (isset($_SERVER[$name])) {
			return $_SERVER[$name];
		}
	}

	/**
	 * 定数を返す
	 *
	 * @access public
	 * @param string $name 定数の名前
	 * @return string 定数の値
	 */
	public function getConstant ($name) {
		return BSConstantHandler::getInstance()->getParameter($name);
	}

	/**
	 * ログを出力
	 *
	 * @access public
	 * @param mixed $message ログメッセージの文字列、又はBSStringFormat
	 * @param mixed $priority 優先順位
	 */
	public function putLog ($message, $priority = BSLogger::DEFAULT_PRIORITY) {
		BSLogManager::getInstance()->put($message, $priority);
	}

	/**
	 * 特別なディレクトリを返す
	 *
	 * @access public
	 * @param string $name ディレクトリの名前
	 * @return BSDirectory ディレクトリ
	 */
	public function getDirectory ($name) {
		return BSDirectoryLayout::getInstance()->getDirectory($name);
	}

	/**
	 * 特別なディレクトリのパスを返す
	 *
	 * @access public
	 * @param string $name ディレクトリの名前
	 * @return string パス
	 */
	public function getPath ($name) {
		return BSDirectoryLayout::getInstance()->getPath($name);
	}

	/**
	 * サーバホストを返す
	 *
	 * @access public
	 * @return string サーバホスト
	 */
	public function getHost () {
		if (!$this->host) {
			$this->host = new BSHost($this->getEnvironment('SERVER_NAME'));
		}
		return $this->host;
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
			$name = BSRequest::getInstance()->getParameter(BSController::MODULE_ACCESSOR);
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
		if (!$this->actions) {
			$this->actions = new BSArray;
		}
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
			throw new BSRegisterException('フォワードが多すぎます。');
		}
		$this->getActionStack()->setParameter(null, $action);
	}

	/**
	 * 呼ばれたアクションを返す
	 *
	 * @access public
	 * @return BSAction アクション
	 */
	public function getAction () {
		return $this->getActionStack()->getIterator()->getLast();
	}

	/**
	 * セキュアアクションを返す
	 *
	 * @access public
	 * @return BSAction アクション
	 */
	public function getSecureAction () {
		return $this->getModule(BS_MODULE_SECURE_MODULE)->getAction(BS_MODULE_SECURE_ACTION);
	}

	/**
	 * NotFoundアクションを返す
	 *
	 * @access public
	 * @return BSAction アクション
	 */
	public function getNotFoundAction () {
		return $this->getModule(BS_MODULE_NOT_FOUND_MODULE)->getAction(BS_MODULE_NOT_FOUND_ACTION);
	}

	/**
	 * 属性を設定
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @param mixed $value 値
	 */
	public function setAttribute ($name, $value) {
		BSSerializeHandler::getInstance()->setAttribute($name, $value);
	}

	/**
	 * 属性を削除
	 *
	 * @access public
	 * @param string $name 属性の名前
	 */
	public function removeAttribute ($name) {
		BSSerializeHandler::getInstance()->removeAttribute($name);
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
		return BSSerializeHandler::getInstance()->getAttribute($name, $date);
	}

	/**
	 * 全ての属性を返す
	 *
	 * @access public
	 * @return mixed[] 全ての属性
	 */
	public function getAttributes () {
		return BSSerializeHandler::getInstance()->getAttributes();
	}

	/**
	 * アプリケーション名を返す
	 *
	 * @access public
	 * @param string $lang 言語
	 * @return string アプリケーション名
	 */
	static public function getName ($lang = 'ja') {
		return BSTranslateManager::getInstance()->execute('APP_NAME', 'BSConstantHandler', $lang);
	}

	/**
	 * アプリケーションのバージョンを返す
	 *
	 * @access public
	 * @return string バージョン
	 */
	static public function getVersion () {
		return BSTranslateManager::getInstance()->execute('APP_VER', 'BSConstantHandler');
	}

	/**
	 * バージョン番号込みのアプリケーション名を返す
	 *
	 * @access public
	 * @param string $lang 言語
	 * @return string アプリケーション名
	 * @static
	 */
	static public function getFullName ($lang = 'ja') {
		return sprintf('%s %s', self::getName($lang), self::getVersion());
	}

	/**
	 * タイムリミットを設定
	 *
	 * @access public
	 * @param integer $seconds 秒単位のタイムリミット
	 */
	public function setTimeLimit ($seconds) {
		return set_time_limit($seconds);
	}

	/**
	 * メモリリミットを設定
	 *
	 * @access public
	 * @param integer $size メモリサイズ
	 */
	public function setMemoryLimit ($size) {
		ini_set('memory_limit', $size);
	}

	/**
	 * レスポンスヘッダを返す
	 *
	 * @access public
	 * @return BSArray レスポンスヘッダの配列
	 */
	public function getHeaders () {
		if (!$this->headers) {
			$this->headers = new BSArray;
		}
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
		$this->getHeaders()->setParameter(
			BSString::stripControlCharacters($name),
			BSString::stripControlCharacters($value)
		);
	}

	/**
	 * レスポンスヘッダを送信
	 *
	 * @access public
	 */
	public function putHeaders () {
	}	
}

/* vim:set tabstop=4: */
