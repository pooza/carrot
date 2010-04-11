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
	protected $host;
	protected $headers;
	protected $actions;
	static private $instance;
	const MODULE_ACCESSOR = 'm';
	const ACTION_ACCESSOR = 'a';
	const ACTION_REGISTER_LIMIT = 20;

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
				return BSRequest::getInstance();
			case 'user':
				return BSUser::getInstance();
			default:
				$message = new BSStringFormat('仮想プロパティ"%s"は未定義です。');
				$message[] = $name;
				throw new BadFunctionCallException($message);
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
			$name = $this->request[self::MODULE_ACCESSOR];
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

			$constants = BSConstantHandler::getInstance();
			if (!BSString::isBlank($value = $constants[$name])) {
				return $value;
			}
		}
		return BSSerializeHandler::getInstance()->getAttribute($name, $date);
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
	 * 全ての属性を返す
	 *
	 * @access public
	 * @return mixed[] 全ての属性
	 */
	public function getAttributes () {
		return BSSerializeHandler::getInstance()->getAttributes();
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
	 * アプリケーション名を返す
	 *
	 * @access public
	 * @param string $lang 言語
	 * @return string アプリケーション名
	 */
	static public function getName ($lang = 'ja') {
		return self::getInstance()->getAttribute('app_name_' . $lang);
	}

	/**
	 * アプリケーションのバージョンを返す
	 *
	 * @access public
	 * @return string バージョン
	 */
	static public function getVersion () {
		return BS_APP_VER;
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
		return self::getName($lang) . ' ' . self::getVersion();
	}
}

/* vim:set tabstop=4: */
