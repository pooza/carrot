<?php
/**
 * @package org.carrot-framework
 * @subpackage view
 */

/**
 * ビュー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSView {
	protected $name;
	protected $nameSuffix;
	private $renderer;
	private $headers = array();
	private $action;
	private $filename;
	const ALERT = 'Alert';
	const ERROR = 'Error';
	const INPUT = 'Input';
	const NONE = null;
	const SUCCESS = 'Success';

	/**
	 * @access public
	 * @param BSAction $action 呼び出し元アクション
	 */
	public function __construct (BSAction $action) {
		$this->action = $action;
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
			case 'useragent':
				return BSRequest::getInstance()->getUserAgent();
			case 'renderer':
				return $this->getRenderer();
			case 'translator':
				return BSTranslateManager::getInstance();
			default:
				throw new BSMagicMethodException('仮想プロパティ"%s"は未定義です。', $name);
		}
	}

	/**
	 * @access public
	 * @param string $method メソッド名
	 * @param mixed[] $values 引数
	 */
	public function __call ($method, $values) {
		if (!method_exists($this->renderer, $method)) {
			throw new BSMagicMethodException('仮想メソッド"%s"は未定義です。', $method);
		}

		// 処理をエンジンに委譲
		$args = array();
		for ($i = 0 ; $i < count($values) ; $i ++) {
			$args[] = '$values[' . $i . ']';
		}
		eval(sprintf('return $this->renderer->%s(%s);', $method, implode(', ', $args)));
	}

	/**
	 * 初期化
	 *
	 * @access public
	 * @return boolean 初期化が成功すればTrue
	 */
	public function initialize () {
		if ($renderer = $this->request->getAttribute('renderer')) {
			$this->setRenderer($renderer);
		}
		if ($filename = $this->request->getAttribute('filename')) {
			$this->setFileName($filename);
		}
		return true;
	}

	/**
	 * 実行
	 *
	 * @access public
	 */
	public function execute () {
	}

	/**
	 * ビュー名を返す
	 *
	 * @access public
	 * @return string ビュー名
	 */
	public function getName () {
		if (!$this->name) {
			preg_match('/^(.+)View$/', get_class($this), $matches);
			$this->name = $matches[1];
		}
		return $this->name;
	}

	/**
	 * ビュー名のサフィックスを返す
	 *
	 * @access public
	 * @return string ビュー名のサフィックス
	 */
	public function getNameSuffix () {
		if (!$this->nameSuffix) {
			$pattern = '/^(.+)(' . self::getNameSuffixes()->join('|') . ')$/';
			if (preg_match($pattern, $this->getName(), $matches)) {
				$this->nameSuffix = $matches[2];
			}
		}
		return $this->nameSuffix;
	}

	/**
	 * レンダリング
	 *
	 * @access public
	 */
	public function render () {
		if (!$this->renderer) {
			throw new BSViewException('レンダラーが指定されていません。');
		} else if (!$this->renderer->validate()) {
			if (!$message = $this->renderer->getError()) {
				$message = 'レンダラーに登録された情報が正しくありません。';
			}
			throw new BSViewException($message);
		}

		$this->setHeader('Content-Type', BSMIMEUtility::getContentType($this->renderer));
		$this->setHeader('Content-Length', $this->renderer->getSize());
		if ($this->useragent->hasBug('cache-control')) {
			$this->setHeader('Cache-Control', null);
			$this->setHeader('Pragma', null);
		}
		$this->putHeaders();

		if ($this->request->getMethod() != BSRequest::HEAD) {
			mb_http_output('pass');
			print $this->renderer->getContents();
		}
	}

	/**
	 * モジュールを返す
	 *
	 * @access public
	 * @return BSModule モジュール
	 */
	public function getModule () {
		return $this->getAction()->getModule();
	}

	/**
	 * アクションを返す
	 *
	 * @access public
	 * @return BSAction アクション
	 */
	public function getAction () {
		return $this->action;
	}

	/**
	 * レンダラーを返す
	 *
	 * @access public
	 * @return BSRenderer レンダラー
	 */
	public function getRenderer () {
		if (!$this->renderer) {
			throw new BSViewException('レンダラーが未設定です。');
		}
		return $this->renderer;
	}

	/**
	 * レンダラーを返す
	 *
	 * getRendererのエイリアス
	 *
	 * @access public
	 * @return BSRenderer レンダラー
	 * @final
	 */
	final public function getEngine () {
		return $this->getRenderer();
	}

	/**
	 * レンダラーを設定
	 *
	 * @access public
	 * @param BSRenderer $renderer レンダラー
	 */
	public function setRenderer (BSRenderer $renderer) {
		$this->renderer = $renderer;
	}

	/**
	 * レンダラーを設定
	 *
	 * setRendererのエイリアス
	 *
	 * @access public
	 * @param BSRenderer $renderer レンダラー
	 * @final
	 */
	final public function setEngine (BSRenderer $renderer) {
		$this->setRenderer($renderer);
	}

	/**
	 * レスポンスヘッダを返す
	 *
	 * BSController::getHeaders()のエイリアス。
	 *
	 * @access public
	 * @return BSArray レスポンスヘッダの配列
	 * @final
	 */
	final public function getHeaders () {
		return $this->controller->getHeaders();
	}

	/**
	 * レスポンスヘッダを設定
	 *
	 * BSController::setHeader()のエイリアス。
	 *
	 * @access public
	 * @param string $name フィールド名
	 * @param string $value フィールド値
	 * @final
	 */
	final public function setHeader ($name, $value) {
		$this->controller->setHeader($name, $value);
	}

	/**
	 * レスポンスヘッダを送信
	 *
	 * BSController::putHeaders()のエイリアス。
	 *
	 * @access public
	 * @final
	 */
	final public function putHeaders () {
		$this->controller->putHeaders();
	}	

	/**
	 * ファイル名を返す
	 *
	 * @access public
	 * @return string ファイル名
	 */
	public function getFileName () {
		return $this->filename;
	}

	/**
	 * ファイル名を設定
	 *
	 * @access public
	 * @param string $name ファイル名
	 */
	public function setFileName ($name, $mode = BSMIMEUtility::ATTACHMENT) {
		$this->filename = $name;
		$name = $this->useragent->getEncodedFileName($name);
		$this->setHeader('Content-Disposition', sprintf('%s; filename="%s"', $mode, $name));
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('%s のビュー "%s"', $this->getModule(), $this->getName());
	}

	/**
	 * 全てのサフィックスを返す
	 *
	 * @access public
	 * @return BSArray 全てのサフィックス
	 */
	static public function getNameSuffixes () {
		return new BSArray(array(
			self::ALERT,
			self::ERROR,
			self::INPUT,
			self::SUCCESS,
		));
	}
}

/* vim:set tabstop=4: */
