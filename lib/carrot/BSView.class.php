<?php
/**
 * @package jp.co.b-shock.carrot
 */

/**
 * Viewのラッパー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 * @abstract
 */
abstract class BSView {
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
				return BSController::getInstance()->getUserAgent();
			case 'renderer':
				return $this->getRenderer();
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
	 * レンダリング前のチェック
	 *
	 * @access protected
	 */
	protected function preRenderCheck () {
		if (!$this->renderer) {
			throw new RenderException('レンダラーが指定されていません。');
		} else if (!$this->renderer->validate()) {
			if (!$message = $this->renderer->getError()) {
				$message = 'レンダラーに登録された情報が正しくありません。';
			}
			throw new RenderException($message);
		}

		preg_match('/^([a-z0-9]+\/[^;]+).*$/i', $this->renderer->getType(), $matches);
		if (!$matches || !in_array($matches[1], BSTypeList::getInstance()->getAttributes())) {
			throw new BSException('メディアタイプ"%s"は正しくありません。', $matches[1]);
		}
	}

	/**
	 * レンダリング
	 *
	 * @access public
	 */
	public function render () {
		$this->preRenderCheck();
		$this->setHeader('Content-Type', $this->renderer->getType());
		$this->setHeader('Content-Length', $this->renderer->getSize());
		if ($this->useragent->hasCachingBug()) {
			$this->setHeader('Cache-Control', null);
			$this->setHeader('Pragma', null);
		}
		foreach ($this->getHeaders() as $name => $value) {
			$this->controller->sendHeader(sprintf('%s: %s', $name, $value));
		}
		mb_http_output('pass');
		print $this->renderer->getContents();
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
			throw new BSException('レンダラーが未設定です。');
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
}

/* vim:set tabstop=4 ai: */
?>