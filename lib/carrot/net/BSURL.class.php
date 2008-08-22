<?php
/**
 * @package org.carrot-framework
 * @subpackage net
 */

/**
 * URL
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSURL implements BSHTTPRedirector {
	private $url;
	private $fullpath;
	private $attributes = array('path' => '/');
	private $parameters;
	private $tinyurl;
	const PATTERN = '/^[a-z]+:\/\/[-_.!~*()a-z0-9;\/?:@&=+$,%#]+$/i';

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param string $url URL
	 */
	public function __construct ($url = null) {
		if ($url) {
			$this->setContents($url);
		} else {
			if (BSController::getInstance()->isSSL()) {
				$this->setAttribute('scheme', 'https');
			} else {
				$this->setAttribute('scheme', 'http');
			}
			$this->setAttribute('host', BSController::getInstance()->getServerHost());
		}
	}

	/**
	 * 内容を返す
	 *
	 * @access public
	 * @return string URL
	 */
	public function getContents () {
		if (!$this->url && $this->getAttributes()) {
			if (!$scheme = $this->getAttribute('scheme')) {
				return null;
			}
			$this->url = $scheme . '://';

			if ($user = $this->getAttribute('user')) {
				$this->url .= $user;
				if ($pass = $this->getAttribute('pass')) {
					$this->url .= ':' . $pass;
				}
				$this->url .= '@';
			}

			if (!$host = $this->getAttribute('host')) {
				$this->url = null;
				return null;
			}
			$this->url .= $host->getName();

			if ($this->getAttribute('port') != BSNetworkService::getPort($scheme)) {
				$this->url .= ':' . $this->getAttribute('port');
			}

			$this->url .= $this->getAttribute('path');

			if ($fragment = $this->getAttribute('fragment')) {
				$this->url .= '#' . $fragment;
			}

			if ($query = $this->getAttribute('query')) {
				$this->url .= '?' . $query;
			}
		}
		return $this->url;
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

		$this->attributes = array('path' => '/');
		foreach (parse_url($url) as $name => $value) {
			$this->setAttribute($name, $value);
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
	 * path以降を返す
	 *
	 * @access public
	 * @return string URLのpath以降
	 */
	public function getFullPath () {
		if (!$this->fullpath && $this->getAttributes()) {
			$this->fullpath .= $this->getAttribute('path');

			if ($query = $this->getAttribute('query')) {
				$this->fullpath .= '?' . $query;
			}

			if ($fragment = $this->getAttribute('fragment')) {
				$this->fullpath .= '#' . $fragment;
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
		if (isset($this->attributes[$name])) {
			return $this->attributes[$name];
		}
	}

	/**
	 * 属性を設定
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @param mixed $value 値
	 */
	public function setAttribute ($name, $value) {
		switch ($name) {
			case 'scheme':
				$this->attributes['scheme'] = $value;
				if (!$this->getAttribute('port')) {
					$this->setAttribute('port', BSNetworkService::getPort($value));
				}
				break;
			case 'host':
				if (!($value instanceof BSHost)) {
					$value = new BSHost($value);
				}
				$this->attributes['host'] = $value;
				break;
			case 'path':
				$value = preg_replace('/^\/*/', '/', $value);
				foreach (parse_url($value) as $name => $attribute) {
					$this->attributes[$name] = $attribute;
				}
				$this->parseQuery();
				break;
			case 'query':
				$this->attributes[$name] = $value;
				$this->parseQuery();
				break;
			case 'port':
			case 'user':
			case 'pass':
			case 'fragment':
				$this->attributes[$name] = $value;
				break;
			default:
				throw new BSNetException('"%s"は正しくない属性名です。', $name);
		}
		$this->url = null;
	}

	/**
	 * 属性を全て返す
	 *
	 * @access public
	 * @return string[] 属性
	 */
	public function getAttributes () {
		return $this->attributes;
	}

	/**
	 * クエリーをパース
	 *
	 * @access private
	 */
	private function parseQuery () {
		parse_str($this->getAttribute('query'), $parameters);
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
		if ($value == '') {
			return;
		}

		$this->parseQuery();
		$this->parameters[$name] = urlencode($value);
		$this->setAttribute('query', BSString::toString($this->parameters, '=', '&'));
	}

	/**
	 * クエリー文字列の全てのパラメータを返す
	 *
	 * @access public
	 * @return string[] パラメータの連想配列
	 */
	public function getParameters () {
		$this->parseQuery();
		return $this->parameters;
	}

	/**
	 * パラメータを設定
	 *
	 * @access public
	 * @param string[] $parameters 属性
	 */
	public function setParameters ($parameters) {
		if (!is_array($parameters)) {
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
		} else if (!in_array($this->getAttribute('scheme'), array('http', 'https'))) {
			throw new BSNetException('URLのスキーム"%s"が正しくありません。');
		}

		try {
			$http = new BSCurlHTTP($this->getAttribute('host'));
			return $http->sendGetRequest($this->getFullPath());
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
	 * 基本情報を文字列で返す
	 *
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('URL "%s"', $this->getContents());
	}
}

/* vim:set tabstop=4 ai: */
?>