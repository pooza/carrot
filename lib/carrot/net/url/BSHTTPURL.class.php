<?php
/**
 * @package org.carrot-framework
 * @subpackage net.url
 */

/**
 * HTTPスキーマのURL
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSHTTPURL extends BSURL implements BSHTTPRedirector {
	private $fullpath;
	private $query;
	private $tinyurl;
	const PATTERN = '/^[a-z]+:(\/\/)?[-_.!~*()a-z0-9;\/?:@&=+$,%#]+$/i';

	/**
	 * @access public
	 * @param string $url URL
	 */
	public function __construct ($url = null) {
		$this->attributes = new BSArray;
		$this->query = new BSWWWFormRenderer;
		if (BSString::isBlank($url)) {
			if (BSRequest::getInstance()->isSSL()) {
				$this['scheme'] = 'https';
			} else {
				$this['scheme'] = 'http';
			}
			$this['host'] = BSController::getInstance()->getHost();
		} else {
			$this->setContents($url);
		}
	}

	/**
	 * 属性を返す
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @return string 属性
	 */
	public function getAttribute ($name) {
		if (($name == 'path') && BSString::isBlank($this->attributes['path'])) {
			$this->attributes['path'] = '/';
		}
		return $this->attributes[$name];
	}

	/**
	 * 属性を設定
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @param mixed $value 値
	 * @return BSURL 自分自身
	 */
	public function setAttribute ($name, $value) {
		$this->fullpath = null;
		switch ($name) {
			case 'path':
				$value = preg_replace('/^\/*/', '/', $value);
				foreach (parse_url($value) as $name => $attribute) {
					$this->attributes[$name] = $attribute;
				}
				$this->query->setContents($this['query']);
				return $this;
			case 'query':
				$this->attributes['query'] = $value;
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
			$this->fullpath = $this['path'];
			if (!BSString::isBlank($this['query'])) {
				$this->fullpath .= '?' . $this['query'];
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
		$this->attributes['query'] = $this->query->getContents();
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
		$this->query->setContents($parameters);
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
			throw new BSHTTPException('"%s"を取得出来ませんでした。', $this->getContents());
		}
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
