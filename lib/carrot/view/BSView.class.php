<?php
/**
 * @package org.carrot-framework
 * @subpackage view
 */

/**
 * 基底ビュー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSView extends BSHTTPResponse {
	protected $nameSuffix;
	protected $action;
	protected $version = '1.0';
	const NONE = null;
	const ERROR = 'Error';
	const INPUT = 'Input';
	const SUCCESS = 'Success';

	/**
	 * @access public
	 * @param BSAction $action 呼び出し元アクション
	 * @param string $suffix ビュー名サフィックス
	 * @param BSRenderer $renderer レンダラー
	 */
	public function __construct (BSAction $action, $suffix, BSRenderer $renderer = null) {
		$this->action = $action;
		$this->nameSuffix = $suffix;

		if (!$renderer) {
			$renderer = new BSRawRenderer;
		}
		$this->setRenderer($renderer);
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
		return BSUtility::executeMethod($this->renderer, $method, $values);
	}

	/**
	 * 初期化
	 *
	 * @access public
	 * @return boolean 初期化が成功すればTrue
	 */
	public function initialize () {
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
		return $this->getAction()->getName() . $this->getNameSuffix();
	}

	/**
	 * ビュー名のサフィックスを返す
	 *
	 * @access public
	 * @return string ビュー名のサフィックス
	 */
	public function getNameSuffix () {
		return $this->nameSuffix;
	}

	/**
	 * レンダリング
	 *
	 * @access public
	 */
	public function render () {
		if (!$this->renderer->validate()) {
			if (!$message = $this->renderer->getError()) {
				$message = 'レンダラーに登録された情報が正しくありません。';
			}
			throw new BSViewException($message);
		}

		$this->setHeader('content-type', BSMIMEUtility::getContentType($this->renderer));
		$this->setHeader('content-length', $this->renderer->getSize());

		$this->putHeaders();
		mb_http_output('pass');
		print $this->renderer->getContents();
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
	 * レスポンスを設定
	 *
	 * @access public
	 * @param BSHTTPResponse $response レスポンス
	 */
	public function setResponse (BSHTTPResponse $response) {
		$this->setRenderer($response->getRenderer());
		foreach ($response->getHeaders() as $header) {
			$this->setHeader($header->getName(), $header->getContents());
		}
	}

	/**
	 * レスポンスヘッダを送信
	 *
	 * @access public
	 */
	public function putHeaders () {
		foreach ($this->controller->getHeaders() as $key => $value) {
			$this->setHeader($key, $value);
		}

		$this->setCacheControl(false);
		if (!BSUser::getInstance()->isAdministrator()) {
			$this->setCacheControl(true);
		} else if (BSRequest::getInstance()->getUserAgent()->hasBug('cache_control')) {
			if (BSRequest::getInstance()->isSSL() || !$this->isHTML()) {
				$this->setCacheControl(true);
			}
		}

		if ($header = $this->getHeader('status')) {
			self::putHeader('HTTP/' . $this->getVersion() . ' ' . $header->getContents());
		}
		foreach ($this->getHeaders() as $name => $header) {
			self::putHeader($header->format(BSMIMEHeader::WITHOUT_CRLF));
		}
	}

	/**
	 * キャッシュ制御を設定
	 *
	 * @access public
	 * @param Boolean $mode キャッシュONならTrue
	 */
	public function setCacheControl ($mode) {
		if (!!$mode) {
			$this->setHeader('Cache-Control', 'private');
			$this->setHeader('Pragma', 'private');
		} else {
			$this->setHeader('Cache-Control', 'no-cache, must-revalidate');
			$this->setHeader('Pragma', 'no-cache');
		}
	}

	/**
	 * ファイル名を設定
	 *
	 * @access public
	 * @param string $filename ファイル名
	 * @param string $mode モード
	 */
	public function setFileName ($name, $mode = BSMIMEUtility::ATTACHMENT) {
		parent::setFileName($this->useragent->encodeFileName($name), $mode);
		$this->filename = $name;
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('%sのビュー "%s"', $this->getModule(), $this->getName());
	}

	/**
	 * 全てのサフィックスを返す
	 *
	 * @access public
	 * @return BSArray 全てのサフィックス
	 */
	static public function getNameSuffixes () {
		return new BSArray(array(
			self::ERROR,
			self::INPUT,
			self::SUCCESS,
		));
	}

	/**
	 * ヘッダを送信
	 *
	 * @access public
	 * @return BSArray 全てのサフィックス
	 */
	static public function putHeader ($header) {
		if (BSRequest::getInstance()->isCLI()) {
			return;
		}
		if (headers_sent()) {
			throw new BSHTTPException('レスポンスヘッダを送信できません。');
		}
		header($header);
	}
}

/* vim:set tabstop=4: */
