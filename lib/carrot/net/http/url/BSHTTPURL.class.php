<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net.http.url
 */

/**
 * HTTPスキーマのURL
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSHTTPURL extends BSURL implements BSHTTPRedirector, BSImageContainer {
	use BSHTTPRedirectorMethods;
	private $fullpath;
	private $useragent;
	private $query;
	private $shortURL;
	private $dirty = false;

	/**
	 * @access protected
	 * @param mixed $contents URL
	 */
	protected function __construct ($contents = null) {
		$this->attributes = new BSArray;
		$this->query = new BSWWWFormRenderer;
		$this->setContents($contents);
	}

	/**
	 * @access public
	 */
	public function __clone () {
		$this->attributes = clone $this->attributes;
		$this->query = clone $this->query;
	}

	/**
	 * 属性を設定
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @param mixed $value 値
	 * @return BSHTTPURL 自分自身
	 */
	public function setAttribute ($name, $value) {
		$this->contents = null;
		$this->fullpath = null;
		switch ($name) {
			case 'path':
				try {
					$values = new BSArray(parse_url($value));
					$this->attributes['path'] = '/' . ltrim($values['path'], '/');
					$this->attributes['fragment'] = $values['fragment'];
					$this['query'] = $values['query'];
					$this->dirty = false;
				} catch (Exception $e) {
					$this->attributes->clear();
					$this->attributes['path'] = $value;
					$this->dirty = true;
				}
				return $this;
			case 'query':
				$this->query->setContents($value);
				return $this;
			case 'fragment':
				$this->attributes[$name] = $value;
				return $this;
		}
		if (mb_ereg('^params?_(.*)$', $name, $matches)) {
			$this->setParameter($matches[1], $value);
			return $this;
		}
		return parent::setAttribute($name, $value);
	}

	/**
	 * URLを設定
	 *
	 * @access public
	 * @param mixed $contents URL
	 */
	public function setContents ($contents) {
		if (is_string($contents) || BSString::isBlank($contents)) {
			$contents = parse_url($contents);
		}
		if (is_array($contents) || ($contents instanceof BSParameterHolder)) {
			$contents = new BSArray($contents);
		}
		if (BSString::isBlank($contents['scheme'])) {
			if ($this->request->isSSL()) {
				$contents['scheme'] = 'https';
			} else {
				$contents['scheme'] = 'http';
			}
		}
		if (BSString::isBlank($contents['host'])) {
			$contents['host'] = $this->controller->getHost();
		}
		parent::setContents($contents);
	}

	/**
	 * path以降を返す
	 *
	 * @access public
	 * @return string URLのpath以降
	 */
	public function getFullPath () {
		if (!$this->fullpath) {
			if (BSString::isBlank($this->attributes['path'])) {
				$this->fullpath = '/';
			} else {
				$this->fullpath = $this['path'];
			}
			if ($this->query->count()) {
				$this->fullpath .= '?' . $this->query->getContents();
			}
			if (!BSString::isBlank($this['fragment'])) {
				$this->fullpath .= '#' . $this['fragment'];
			}
		}
		return $this->fullpath;
	}

	/**
	 * パラメータを返す
	 *
	 * @access public
	 * @param string $name パラメータの名前
	 * @return string パラメータ
	 */
	public function getParameter ($name) {
		return $this->query[$name];
	}

	/**
	 * パラメータを設定
	 *
	 * @access public
	 * @param string $name パラメータの名前
	 * @param string $value パラメータの値
	 */
	public function setParameter ($name, $value) {
		if (BSString::isBlank($value)) {
			return;
		}
		$this->query[(string)$name] = $value;
		$this->fullpath = null;
		$this->contents = null;
	}

	/**
	 * クエリー文字列の全てのパラメータを返す
	 *
	 * @access public
	 * @return BSArray パラメータの配列
	 */
	public function getParameters () {
		return $this->query->getParameters();
	}

	/**
	 * パラメータを設定
	 *
	 * @access public
	 * @param mixed $params パラメータ文字列、又は配列
	 */
	public function setParameters ($params) {
		$this->query->setParameters($params);
		$this->fullpath = null;
	}

	/**
	 * 対象UserAgentを返す
	 *
	 * @access public
	 * @return BSUserAgent 対象UserAgent
	 */
	public function getUserAgent () {
		return $this->useragent;
	}

	/**
	 * 対象UserAgentを設定
	 *
	 * @access public
	 * @param BSUserAgent $useragent 対象UserAgent
	 */
	public function setUserAgent (BSUserAgent $useragent) {
		if ($this->useragent) {
			if ($this->useragent === $useragent) {
				return;
			}
			throw new BSUserAgentException('対象URLは設定済みです。');
		}

		$this->useragent = $useragent;
		if (!$this->isForeign()) {
			$this->setParameters($useragent->getQuery());
		}
		return $this;
	}

	/**
	 * Curlでフェッチして文字列で返す
	 *
	 * @access public
	 * @param string $class HTTPクラス名
	 * @return string フェッチした内容
	 */
	public function fetch ($class = 'BSCurlHTTP') {
		try {
			$http = new $class($this['host']);
			$response = $http->sendGET($this->getFullPath());
			return $response->getRenderer()->getContents();
		} catch (BSException $e) {
			throw new BSHTTPException($this . 'を取得できません。');
		}
	}

	/**
	 * favicon画像を返す
	 *
	 * @access public
	 * @return BSImage favicon画像
	 */
	public function getFavicon () {
		return $this->getImageFile('favicon');
	}

	/**
	 * キャッシュをクリア
	 *
	 * @access public
	 * @param string $size
	 */
	public function removeImageCache ($size) {
		if ($file = $this->getImageFile($size)) {
			$file->removeImageCache('image');
		}
	}

	/**
	 * 画像の情報を返す
	 *
	 * @access public
	 * @param string $size サイズ名
	 * @param integer $pixel ピクセルサイズ
	 * @param integer $flags フラグのビット列
	 * @return BSArray 画像の情報
	 */
	public function getImageInfo ($size, $pixel = null, $flags = null) {
		if ($file = $this->getImageFile($size)) {
			$info = (new BSImageManager)->getImageInfo($file, $size, $pixel, $flags);
			$info['alt'] = $this->getID();
			return $info;
		}
	}

	/**
	 * 画像ファイルを返す
	 *
	 * @access public
	 * @param string $size サイズ名
	 * @return BSImageFile 画像ファイル
	 */
	public function getImageFile ($size) {
		switch ($size) {
			case 'favicon':
				return (new BSGoogleFaviconsService)->getImageFile($this['host']);
			case 'qr':
				return (new BSGoogleChartService)->getQRCodeImageFile($this->getContents());
		}
	}

	/**
	 * 画像ファイルベース名を返す
	 *
	 * @access public
	 * @param string $size サイズ名
	 * @return string 画像ファイルベース名
	 */
	public function getImageFileBaseName ($size) {
		return $this->getID();
	}

	/**
	 * コンテナのIDを返す
	 *
	 * コンテナを一意に識別する値。
	 * ファイルならinode、DBレコードなら主キー。
	 *
	 * @access public
	 * @return integer ID
	 */
	public function getID () {
		return BSCrypt::digest($this->getName());
	}

	/**
	 * コンテナの名前を返す
	 *
	 * @access public
	 * @return string 名前
	 */
	public function getName () {
		return $this->getContents();
	}

	/**
	 * コンテナのラベルを返す
	 *
	 * @access public
	 * @param string $language 言語
	 * @return string ラベル
	 */
	public function getLabel ($language = 'ja') {
		return $this->getID();
	}

	/**
	 * 外部のURLか？
	 *
	 * @access public
	 * @param mixed $host 対象ホスト
	 * @return boolean 外部のURLならTrue
	 */
	public function isForeign ($host = null) {
		if ($host) {
			if ($host instanceof BSHTTPURL) {
				$host = $host['host'];
			} else if (!($host instanceof BSHost)) {
				$host = new BSHost($host);
			}
		} else {
			$host = $this->controller->getHost();
		}
		return $this['host']->isForeign($host);
	}

	/**
	 * 短縮URLを返す
	 *
	 * @access public
	 * @return BSURL 短縮URL
	 */
	public function getShortURL () {
		if (!$this->shortURL) {
			$service = $this->loader->createObject(BS_NET_URL_SHORTER, 'Service');
			if (!$service || !($service instanceof BSURLShorter)) {
				throw new BSHTTPException('URL短縮サービスが取得できません。');
			}
			$key = BSCrypt::digest([get_class($this), $this->getContents()]);
			if ($url = $this->controller->getAttribute($key)) {
				$this->shortURL = BSURL::create($url);
			} else {
				$this->shortURL = $service->getShortURL($this);
				$this->controller->setAttribute($key, $this->shortURL->getContents());
			}
		}
		return $this->shortURL;
	}

	/**
	 * 短縮URLを返す
	 *
	 * getShortURLのエイリアス
	 *
	 * @access public
	 * @return BSURL 短縮URL
	 * @final
	 */
	final public function getTinyURL () {
		return $this->getShortURL();
	}

	/**
	 * リダイレクト対象
	 *
	 * @access public
	 * @return BSURL
	 */
	public function getURL () {
		return $this;
	}

	/**
	 * リダイレクト
	 *
	 * @access public
	 * @return string ビュー名
	 */
	public function redirect () {
		$url = $this->createURL();
		$url->setParameters($this->request->getUserAgent()->getQuery());
		if (!$this->isForeign()) {
			$this->user->setAttribute('errors', $this->request->getErrors());
		}
		$this->controller->setHeader('Location', $url->getContents());
		return BSView::NONE;
	}
}

/* vim:set tabstop=4: */
