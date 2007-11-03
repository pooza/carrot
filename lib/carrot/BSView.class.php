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
abstract class BSView extends View {
	private $engine;
	private $headers = array();
	private $filename;
	const ATTACHMENT = 'attachment';
	const INLINE = 'inline';

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
				return $this->getContext()->getController();
			case 'request':
				return $this->getContext()->getRequest();
			case 'user':
				return $this->getContext()->getUser();
			case 'context':
				return $this->getContext();
			case 'useragent':
				return $this->getContext()->getController()->getUserAgent();
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
		if (!method_exists($this->getEngine(), $method)) {
			throw new BSException('仮想メソッド"%s"は未定義です。', $method);
		}

		// 処理をエンジンに委譲
		$args = array();
		for ($i = 0 ; $i < count($values) ; $i ++) {
			$args[] = '$values[' . $i . ']';
		}
		eval(sprintf('return $this->getEngine()->%s(%s);', $method, implode(', ', $args)));
	}

	/**
	 * レンダリング前のチェック
	 *
	 * @access protected
	 */
	protected function preRenderCheck () {
		if (!$this->getEngine()) {
			throw new BSException('ビューエンジンが指定されていません。');
		} else if (!$this->getEngine()->validate()) {
			if (!$message = $this->getEngine()->getError()) {
				$message = 'ビューエンジンに登録された情報が正しくありません。';
			}
			throw new BSException($message);
		}

		$types = BSTypeList::getInstance();
		preg_match('/^([a-z0-9]+\/[^;]+).*$/i', $this->getEngine()->getType(), $matches);
		if (!$matches || !in_array($matches[1], $types->getAttributes())) {
			throw new BSException('メディアタイプ"%s"は正しくありません。', $matches[1]);
		}
	}

	/**
	 * レンダリング
	 *
	 * @access public
	 */
	public function & render () {
		$this->preRenderCheck();
		$contents = $this->getEngine()->getContents();
		$this->setHeader('Content-Type', $this->getEngine()->getType());

		// WinIEのバグ対応
		if ($this->controller->isSSL()
			&& ($this->useragent->getAttribute('Platform') == 'Win32')
			&& ($this->useragent->getType() == 'MSIE')
			&& ($this->useragent->getAttribute('MajorVer') < 7)) {

			$this->setHeader('Cache-Control', null);
			$this->setHeader('Pragma', null);
		}

		if ($this->controller->getRenderMode() == View::RENDER_CLIENT) {
			$this->setHeader('Content-Length', strlen($contents));
			foreach ($this->getHeaders() as $name => $value) {
				$this->controller->sendHeader(sprintf('%s: %s', $name, $value));
			}
			mb_http_output('pass');
			print $contents;
		}
		return $contents;
	}

	/**
	 * 全ての属性の名前を返す
	 *
	 * @access public
	 * @return string[] 全ての属性の名前
	 */
	public function getAttributeNames () {
		return array_keys($this->getAttributes());
	}

	/**
	 * 属性を返す
	 *
	 * @access public
	 * @param string $name 属性名
	 * @return mixed 属性
	 */
	public function & getAttribute ($name) {
		if (isset($this->getEngine()->$name)) {
			return $this->getEngine()->$name;
		}
	}

	/**
	 * 全ての属性を返す
	 *
	 * @access public
	 * @return mixed[] 全ての属性
	 */
	public function getAttributes () {
		return get_object_vars($this->getEngine());
	}

	/**
	 * ビューエンジンを返す
	 *
	 * @access public
	 * @return BSViewEngine ビューエンジン
	 */
	public function & getEngine () {
		if (!$this->engine) {
			throw new BSException('ビューエンジンが未設定です。');
		}
		return $this->engine;
	}

	/**
	 * ビューエンジンを設定
	 *
	 * @access public
	 * @param BSViewEngine $engine ビューエンジン
	 */
	public function setEngine (BSViewEngine $engine) {
		$this->engine = $engine;
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

	public function setAttribute ($name, $value) {}
	public function setAttributeByRef ($name, &$value) {}
	public function setAttributes ($attributes) {}
	public function setAttributesByRef (&$attributes) {}
	public function & removeAttribute ($name) {}
	public function clearAttributes () {}
}

/* vim:set tabstop=4 ai: */
?>