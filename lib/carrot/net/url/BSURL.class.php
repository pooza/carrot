<?php
/**
 * @package org.carrot-framework
 * @subpackage net.url
 */

/**
 * URL
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSURL implements BSHTTPRedirector, ArrayAccess, BSAssignable {
	private $contents;
	private $fullpath;
	private $attributes;
	private $parameters;
	private $tinyurl;
	const PATTERN = '/^[a-z]+:\/\/[-_.!~*()a-z0-9;\/?:@&=+$,%#]+$/i';

	/**
	 * @access public
	 * @param string $url URL
	 */
	public function __construct ($url = null) {
		if ($url) {
			$this->setContents($url);
		} else {
			if (BSRequest::getInstance()->isSSL()) {
				$this['scheme'] = 'https';
			} else {
				$this['scheme'] = 'http';
			}
			$this['host'] = BSController::getInstance()->getHost();
		}
	}

	/**
	 * 内容を返す
	 *
	 * @access public
	 * @return string URL
	 */
	public function getContents () {
		if (!$this->contents && $this->getAttributes()) {
			if (!$this->contents = $this->getHeadString()) {
				return null;
			}
			$this->contents .= $this->getFullPath();
		}
		return $this->contents;
	}

	/**
	 * URLを設定
	 *
	 * @access public
	 * @param string $url URL
	 */
	public function setContents ($url) {
		if (!preg_match(self::PATTERN, $url)) {
			return false;
		}

		$this->attributes = null;
		foreach (parse_url($url) as $name => $value) {
			$this[$name] = $value;
		}
	}

	/**
	 * URLを設定
	 *
	 * setContentsのエイリアス
	 *
	 * @access public
	 * @param string $url URL
	 * @final
	 */
	final public function setURL ($url) {
		$this->setContents($url);
	}

	/**
	 * フルパスを除いた前半を返す
	 *
	 * @access private
	 * @return string 前半
	 */
	private function getHeadString () {
		$head = null;

		if (!$this['scheme']) {
			return null;
		}
		$head = $this['scheme'] . '://';

		if ($this['user']) {
			$head .= $this['user'];
			if ($this['pass']) {
				$head .= ':' . $this['pass'];
			}
			$head .= '@';
		}

		if (!$this['host']) {
			return null;
		}
		$head .= $this['host']->getName();

		if ($this['port'] != BSNetworkService::getPort($this['scheme'])) {
			$head .= ':' . $this['port'];
		}

		return $head;
	}

	/**
	 * path以降を返す
	 *
	 * @access public
	 * @return string URLのpath以降
	 */
	public function getFullPath () {
		if (!$this->fullpath && $this->getAttributes()) {
			$this->fullpath = $this['path'];
			if ($this['query']) {
				$this->fullpath .= '?' . $this['query'];
			}
			if ($this['fragment']) {
				$this->fullpath .= '#' . $this['fragment'];
			}
		}
		return $this->fullpath;
	}

	/**
	 * 属性を返す
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @return string 属性
	 */
	public function getAttribute ($name) {
		return $this->getAttributes()->getParameter($name);
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
		switch ($name) {
			case 'scheme':
				$this->getAttributes()->setParameter('scheme', $value);
				if (!$this['port']) {
					$this['port'] = BSNetworkService::getPort($value);
				}
				break;
			case 'host':
				if (($value instanceof BSHost) == false) {
					$value = new BSHost($value);
				}
				$this->getAttributes()->setParameter('host', $value);
				break;
			case 'path':
				$value = preg_replace('/^\/*/', '/', $value);
				foreach (parse_url($value) as $name => $attribute) {
					$this->getAttributes()->setParameter($name, $attribute);
				}
				$this->parseQuery();
				break;
			case 'query':
				$this->getAttributes()->setParameter($name, $value);
				$this->parseQuery();
				break;
			case 'port':
			case 'user':
			case 'pass':
			case 'fragment':
				$this->getAttributes()->setParameter($name, $value);
				break;
			default:
				throw new BSNetException('"%s"は正しくない属性名です。', $name);
		}
		$this->contents = null;
		return $this;
	}

	/**
	 * 属性を全て返す
	 *
	 * @access public
	 * @return string[] 属性
	 */
	public function getAttributes () {
		if (!$this->attributes) {
			$this->attributes = new BSArray;
			$this->attributes['path'] = '/';
		}
		return $this->attributes;
	}

	/**
	 * クエリーをパース
	 *
	 * @access private
	 */
	private function parseQuery () {
		parse_str($this['query'], $parameters);
		$this->parameters = new BSArray($parameters);
	}

	/**
	 * パラメータを返す
	 *
	 * @access public
	 * @param string $name パラメータの名前
	 * @return string パラメータ
	 */
	public function getParameter ($name) {
		return $this->getParameters()->getParameter($name);
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

		$this->parseQuery();
		$this->parameters[$name] = urlencode($value);
		$this['query'] = BSString::toString($this->parameters, '=', '&');
	}

	/**
	 * クエリー文字列の全てのパラメータを返す
	 *
	 * @access public
	 * @return BSArray パラメータの配列
	 */
	public function getParameters () {
		$this->parseQuery();
		return $this->parameters;
	}

	/**
	 * パラメータを設定
	 *
	 * @access public
	 * @param string[] $parameters パラメータ
	 */
	public function setParameters ($parameters) {
		if (!BSArray::isArray($parameters)) {
			parse_str($parameters, $parameters);
		}
		foreach ($parameters as $name => $value) {
			$this->setParameter($name, $value);
		}
	}

	/**
	 * Curlでフェッチして文字列で返す
	 *
	 * @access public
	 * @return string フェッチした内容
	 */
	public function fetch () {
		if (!$this->validate()) {
			throw new BSNetException('URLが正しくありません。');
		} else if (!in_array($this['scheme'], array('http', 'https'))) {
			throw new BSNetException('URLのスキーム"%s"が正しくありません。');
		}

		try {
			$http = new BSCurlHTTP($this['host']);
			$response = $http->sendGetRequest($this->getFullPath());
			return $response->getRenderer()->getContents();
		} catch (BSException $e) {
			throw new BSHTTPException('"%s"を取得出来ませんでした。', $this->getContents());
		}
	}

	/**
	 * 妥当なURLか？
	 *
	 * @access public
	 * @return boolean 妥当ならtrue
	 */
	public function validate () {
		return ($this->getContents() != null);
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

	/**
	 * 要素が存在するか？
	 *
	 * @access public
	 * @param string $key 添え字
	 * @return boolean 要素が存在すればTrue
	 */
	public function offsetExists ($key) {
		return isset($this->attribute[$key]);
	}

	/**
	 * 要素を返す
	 *
	 * @access public
	 * @param string $key 添え字
	 * @return mixed 要素
	 */
	public function offsetGet ($key) {
		return $this->getAttribute($key);
	}

	/**
	 * 要素を設定
	 *
	 * @access public
	 * @param string $key 添え字
	 * @param mixed 要素
	 */
	public function offsetSet ($key, $value) {
		$this->setAttribute($key, $value);
	}

	/**
	 * 要素を削除
	 *
	 * @access public
	 * @param string $key 添え字
	 */
	public function offsetUnset ($key) {
		$this->setAttribute($key, null);
	}

	/**
	 * アサインすべき値を返す
	 *
	 * @access public
	 * @return mixed アサインすべき値
	 */
	public function getAssignValue () {
		return $this->getContents();
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('URL "%s"', $this->getContents());
	}
}

/* vim:set tabstop=4: */
