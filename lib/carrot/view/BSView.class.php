<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage view
 */

/**
 * 基底ビュー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
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
			$renderer = $this->createDefaultRenderer();
		}
		$this->setRenderer($renderer);

		$this->setHeader('X-Frame-Options', BS_VIEW_FRAME_OPTIONS);
		$this->setHeader('X-Content-Type-Options', BS_VIEW_CONTENT_TYPE_OPTIONS);
		$this->setHeader('X-UA-Compatible', BS_VIEW_UA_COMPATIBLE);
		$this->setHeader('X-XSS-Protection', BS_VIEW_XSS_PROTECTION);
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
			case 'useragent':
				return BSRequest::getInstance()->getUserAgent();
			case 'translator':
				return BSTranslateManager::getInstance();
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
	 * 規定のレンダラーを生成して返す
	 *
	 * @access protected
	 * @return BSRenderer レンダラー
	 */
	protected function createDefaultRenderer () {
		return new BSRawRenderer;
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

		$this->putHeaders();
		mb_http_output('pass');
		echo $this->renderer->getContents();
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
	 * レスポンスヘッダを送信
	 *
	 * @access public
	 */
	public function putHeaders () {
		foreach ($this->controller->getHeaders() as $key => $value) {
			$this->setHeader($key, $value);
		}

		$this->setCacheControl($this->isCacheable());

		if ($header = $this->getHeader('status')) {
			self::putHeader('HTTP/' . $this->getVersion() . ' ' . $header->getContents());
		}
		foreach ($this->getHeaders() as $name => $header) {
			self::putHeader($header->format(BSMIMEHeader::WITHOUT_CRLF));
		}
	}

	/**
	 * HTTPキャッシュ有効か
	 *
	 * @access public
	 * @return boolean 有効ならTrue
	 */
	public function isCacheable () {
		if ($this->useragent->hasBug('cache_control')) {
			if ($this->request->isSSL() || !$this->isHTML()) {
				return true;
			}
		}
		return (BS_APP_HTTP_CACHE_MODE != 'no-cache') && $this->user->isGuest();
	}

	/**
	 * キャッシュ制御を設定
	 *
	 * @access public
	 * @param boolean $mode キャッシュONならTrue
	 */
	public function setCacheControl ($mode) {
		$expires = BSDate::getNow();
		if (!!$mode) {
			$value = new BSStringFormat('%s, max-age=%d');
			$value[] = BS_APP_HTTP_CACHE_MODE;
			$value[] = BS_APP_HTTP_CACHE_SECONDS;
			$this->setHeader('Cache-Control', $value->getContents());
			$this->setHeader('Pragma', BS_APP_HTTP_CACHE_MODE);
			$expires['second'] = '+' . BS_APP_HTTP_CACHE_SECONDS;
		} else {
			$this->setHeader('Cache-Control', 'no-cache, must-revalidate');
			$this->setHeader('Pragma', 'no-cache');
			$expires = BSDate::getNow();
			$expires['hour'] = '-1';
		}
		$this->setHeader('Expires', $expires->format(DateTime::RFC1123));
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
		return new BSArray([
			self::ERROR,
			self::INPUT,
			self::SUCCESS,
		]);
	}

	/**
	 * ヘッダを送信
	 *
	 * @access public
	 * @param string $header ヘッダ
	 * @static
	 */
	static public function putHeader ($header) {
		if (BSRequest::getInstance() instanceof BSConsoleRequest) {
			return;
		}
		if (headers_sent()) {
			throw new BSViewException('レスポンスヘッダを送信できません。');
		}
		header($header);
	}
}

/* vim:set tabstop=4: */
