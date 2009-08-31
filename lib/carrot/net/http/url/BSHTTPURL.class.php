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
class BSHTTPURL extends BSURL implements BSHTTPRedirector {
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
