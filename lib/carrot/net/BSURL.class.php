<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net
 */

/**
 * URL
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSURL {
	private $url;
	private $fullpath;
	private $attributes = array('path' => '/');
	private $parameters = array();
	const PATTERN = '/^[a-z]+:\/\/[-_.!~*()a-z0-9;\/?:@&=+$,%#]+$/i';

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param string $url URL
	 */
	public function __construct ($url = null) {
		if ($url) {
			$this->setURL($url);
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

			if ($this->getAttribute('port') != BSServiceList::getPort($scheme)) {
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
	 * URLを設定する
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
	 * URLを設定する
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
					$this->setAttribute('port', BSServiceList::getPort($value));
				}
				break;
			case 'host':
				if (!is_object($value)) {
					$value = new BSHost($value);
				}
				$this->attributes['host'] = $value;
				break;
			case 'path':
				$value = preg_replace('/^\/*/', '/', $value);
				foreach (parse_url($value) as $name => $attribute) {
					$this->attributes[$name] = $attribute;
				}
				$this->parameters = array();
				break;
			case 'query':
				$this->attributes[$name] = $value;
				$this->parameters = array();
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
	 * パラメータを返す
	 *
	 * @access public
	 * @param string $name パラメータの名前
	 * @return string パラメータ
	 */
	public function getParameter ($name) {
		$parameters = $this->getParameters();
		if (isset($parameters[$name])) {
			return $parameters[$name];
		}
	}

	/**
	 * クエリー文字列の全てのパラメータを返す
	 *
	 * @access public
	 * @return string[] パラメータの連想配列
	 */
	public function getParameters () {
		if ($this->getAttribute('query') && !$this->parameters) {
			foreach (explode('&', $this->getAttribute('query')) as $i) {
				$param = explode('=', $i);
				if ($param[1] == '') {
					continue;
				}

				$param[1] = urldecode($param[1]);
				$param[1] = BSString::convertEncoding($param[1]);
				$this->parameters[$param[0]] = $param[1];
			}
		}
		return $this->parameters;
	}

	/**
	 * パラメータを設定する
	 *
	 * @access public
	 * @param string[] $parameters 属性
	 */
	public function setParameters ($parameters) {
		$this->getParameters();
		foreach ($parameters as $name => $value) {
			if ($value) {
				$this->parameters[$name] = urlencode($value);
			}
		}
		$this->setAttribute('query', BSString::toString($this->parameters, '=', '&'));
	}

	/**
	 * ケータイ環境ならば、セッションIDを付加する
	 *
	 * @access public
	 */
	public function addSessionID () {
		$controller = BSController::getInstance();
		if ($controller->getUserAgent()->isMobile()) {
			$params = $this->getParameters();
			$params[session_name()] = session_id();
			if ($controller->isDebugMode()) {
				$params['ua'] = $controller->getUserAgent()->getName();
			}
			$this->setParameters($params);
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