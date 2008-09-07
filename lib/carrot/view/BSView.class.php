<?php
/**
 * @package org.carrot-framework
 * @subpackage view
 */

/**
 * ビュー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 * @abstract
 */
abstract class BSView {
	private $name;
	private $renderer;
	private $headers = array();
	private $action;
	private $filename;
	const ATTACHMENT = 'attachment';
	const INLINE = 'inline';
	const ALERT = 'Alert';
	const ERROR = 'Error';
	const INPUT = 'Input';
	const NONE = null;
	const SUCCESS = 'Success';

	public function __construct (BSAction $action) {
		$this->action = $action;
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
				return BSTranslator::getInstance();
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
		if (!method_exists($this->renderer, $method)) {
			throw new BSException('仮想メソッド"%s"は未定義です。', $method);
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
		return true;
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
	 * レンダリング
	 *
	 * @access public
	 */
	public function render () {
		if (!$this->renderer) {
			throw new BSRenderException('レンダラーが指定されていません。');
		} else if (!$this->renderer->validate()) {
			if (!$message = $this->renderer->getError()) {
				$message = 'レンダラーに登録された情報が正しくありません。';
			}
			throw new BSRenderException($message);
		}

		$this->setContentType();
		$this->setHeader('Content-Length', $this->renderer->getSize());
		if ($this->useragent->hasCachingBug()) {
			$this->setHeader('Cache-Control', null);
			$this->setHeader('Pragma', null);
		}
		foreach ($this->getHeaders() as $name => $value) {
			if ($name) {
				$this->controller->sendHeader(sprintf('%s: %s', $name, $value));
			} else {
				$this->controller->sendHeader($value);
			}
		}
		mb_http_output('pass');
		print $this->renderer->getContents();
	}

	/**
	 * Content-Typeを設定
	 *
	 * @access private
	 */
	private function setContentType () {
		if ($this->renderer instanceof BSTextRenderer) {
			if (!$charset = mb_preferred_mime_name($this->renderer->getEncoding())) {
				throw new BSRenderException(
					'エンコード"%s"が正しくありません。',
					$this->renderer->getEncoding()
				);
			}
			$type = sprintf('%s; charset=%s', $this->renderer->getType(), $charset);
			$this->setHeader('Content-Type', $type);
		} else {
			$this->setHeader('Content-Type', $this->renderer->getType());
		}
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
			throw new BSRenderException('レンダラーが未設定です。');
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
	 * 定義済みのHTTPヘッダ一式を返す
	 *
	 * @access public
	 * @return string[] HTTPヘッダ
	 */
	public function getHeaders () {
		return $this->headers;
	}

	/**
	 * HTTPヘッダを定義
	 *
	 * @access public
	 * @param string $name ヘッダ名
	 * @param string $value 値
	 */
	public function setHeader ($name, $value) {
		$this->headers[$name] = $value;
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
	public function setFileName ($name, $mode = self::INLINE) {
		$this->filename = $name;
		$name = $this->useragent->getEncodedFileName($name);
		$this->setHeader('Content-Disposition', sprintf('%s; filename="%s"', $mode, $name));
	}

	/**
	 * 基本情報を文字列で返す
	 *
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('ビュー "%s"', $this->getName());
	}
}

/* vim:set tabstop=4 ai: */
?>