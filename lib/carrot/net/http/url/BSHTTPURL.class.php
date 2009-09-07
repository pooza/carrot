<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.url
 */

/**
 * HTTPスキーマのURL
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSHTTPURL extends BSURL implements BSHTTPRedirector, BSImageContainer {
	private $fullpath;
	private $query;
	private $tinyurl;

	/**
	 * @access protected
	 * @param mixed $contents URL
	 */
	protected function __construct ($contents = null) {
		$this->attributes = new BSArray;
		$this->query = new BSWWWFormRenderer;
		if (BSString::isBlank($contents)) {
			if (BSRequest::getInstance()->isSSL()) {
				$this['scheme'] = 'https';
			} else {
				$this['scheme'] = 'http';
			}
			$this['host'] = BSController::getInstance()->getHost();
		} else {
			$this->setContents($contents);
		}
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
		$this->fullpath = null;
		switch ($name) {
			case 'path':
				$values = new BSArray(parse_url($value));
				$this->attributes['path'] = $values['path'];
				$this->attributes['fragment'] = $values['fragment'];
				$this['query'] = $values['query'];
				return $this;
			case 'query':
				$this->query->setContents($value);
				return $this;
			case 'fragment':
				$this->attributes[$name] = $value;
				return $this;
		}
		return parent::setAttribute($name, $value);
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
		if (is_array($name) || is_object($name)) {
			throw new BSRegisterException('パラメータ名が文字列ではありません。');
		} else if (BSString::isBlank($value)) {
			return;
		}
		$this->query[$name] = $value;
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
	 * @param mixed $parameters パラメータ文字列、又は配列
	 */
	public function setParameters ($parameters) {
		if (!BSArray::isArray($parameters)) {
			parse_str($parameters, $parsed);
			$parameters = (array)$parsed;
		}
		$this->query->setParameters($parameters);
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
			$response = $http->sendGetRequest($this->getFullPath());
			return $response->getRenderer()->getContents();
		} catch (BSException $e) {
			throw new BSHTTPException('"%s"を取得できません。', $this->getContents());
		}
	}

	/**
	 * favicon画像を返す
	 *
	 * @access public
	 * @return BSImage favicon画像
	 */
	public function getFavicon () {
		try {
			$url = clone $this;
			$url['path'] = '/favicon.ico';

			BSUtility::includeFile('class.ico.php');
			$ico = new Ico($url->getContents());
			$image = new BSImage;
			$image->setImage($ico->getIcon(0));
			$image->setType(BSMIMEType::getType('png'));

			return $image;
		} catch (BSHTTPException $e) {
			return null;
		} catch (BSImageException $e) {
			return null;
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
	public function getImageInfo ($size = 'favicon', $pixel = null, $flags = null) {
		if ($file = $this->getImageFile()) {
			$url = BSURL::getInstance();
			$url['path'] = '/carrotlib/images/favicon/' . $file->getName();
			return new BSArray(array(
				'width' => $file->getEngine()->getWidth(),
				'height' => $file->getEngine()->getHeight(),
				'type' => $file->getEngine()->getType(),
				'url' => $url->getContents(),
				'alt' => $this->getID(),
			));
		}
	}

	/**
	 * 画像ファイルを返す
	 *
	 * @access public
	 * @param string $size サイズ名
	 * @return BSImageFile 画像ファイル
	 */
	public function getImageFile ($size = 'favicon') {
		$dir = BSController::getInstance()->getDirectory('favicon');
		$name = $this->getImageFileBaseName();
		if (!$file = $dir->getEntry($name, 'BSImageFile')) {
			$file = BSFile::getTemporaryFile('.png', 'BSImageFile');
			$file->setEngine($this->getFavicon());
			$file->save();
			$file->setName($name);
			$file->moveTo($dir);
		}
		return $file;
	}

	/**
	 * 画像ファイルを設定する
	 *
	 * @access public
	 * @param BSImageFile $file 画像ファイル
	 * @param string $size サイズ名
	 */
	public function setImageFile (BSImageFile $file, $size = 'favicon') {
		throw new BSImageException('%sの画像ファイルを設定できません。', $this);
	}

	/**
	 * 画像ファイルベース名を返す
	 *
	 * @access public
	 * @param string $size サイズ名
	 * @return string 画像ファイルベース名
	 */
	public function getImageFileBaseName ($size = 'favicon') {
		return BSCrypt::getSHA1($this->getID());
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
		return $this['host']->getName();
	}

	/**
	 * コンテナの名前を返す
	 *
	 * @access public
	 * @return string 名前
	 */
	public function getName () {
		return $this->getID();
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
	 * TinyURLを返す
	 *
	 * @access public
	 * @return BSURL TinyURL
	 */
	public function getTinyURL () {
		if (!$this->tinyurl) {
			$service = new BSTinyURL; 
			$this->tinyurl = $service->encode($this);
		}
		return $this->tinyurl;
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
		return BSController::getInstance()->redirect($this);
	}
}

/* vim:set tabstop=4: */
