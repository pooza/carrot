<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage useragent
 */

/**
 * browscap
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSBrowscap extends BSList {
	private $url;
	private $file;
	static private $instance;

	/**
	 * コンストラクタ
	 *
	 * @access private
	 */
	private function __construct () {
		// コンストラクタからのインスタンス生成を禁止
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSBrowscap インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (!self::$instance) {
			self::$instance = new BSBrowscap();
		}
		return self::$instance;
	}

	/**
	 * ディープコピーを行う
	 *
	 * @access public
	 */
	public function __clone () {
		throw new BSException('"%s"はコピー出来ません。', __CLASS__);
	}

	/**
	 * ユーザーエージェント情報を返す
	 *
	 * @access public
	 * @param string $name ユーザーエージェント名
	 * @return string[] ユーザーエージェント情報
	 */
	public function getInfo ($name = null) {
		if (!$name) {
			$name = BSController::getInstance()->getEnvironment('HTTP_USER_AGENT');
		}
		$attributes = array('Name' => $name);
		foreach ($this->getMatchedNames($name) as $key) {
			$attributes = array_merge($attributes, $this->getAttribute($key));
		}
		return $attributes;
	}

	/**
	 * 全ての属性を返す
	 *
	 * @access public
	 * @return mixed[] 全ての属性
	 */
	public function getAttributes () {
		if (!$this->attributes) {
			$this->attributes = BSController::getInstance()->getAttribute(
				get_class($this),
				BSDate::getNow()->setAttribute('day', '-7')
			);
			if (!$this->attributes) {
				foreach ($this->getFile()->getContents() as $key => $values) {
					if (!isset($values['Parent'])) {
						$values['Parent'] = null;
					}
					$pattern = preg_quote(strtolower($key), '/');
					$pattern = str_replace(array('\*', '\?'), array('.*', '.'), $pattern);
					$pattern = '/^' . $pattern . '/i';
					$values['Pattern'] = $pattern;
					$this->attributes[$key] = $values;
				}
				BSController::getInstance()->setAttribute(get_class($this), $this->attributes);
			}
		}
		return $this->attributes;
	}

	/**
	 * browscap.iniファイルを返す
	 *
	 * @access private
	 * @return BSConfigFile browscap.iniファイル
	 */
	private function getFile () {
		if (!$this->file) {
			$dir = BSController::getInstance()->getDirectory('tmp');
			$this->file = $dir->createEntry('browscap.ini', 'BSConfigFile');
			$this->file->setContents($this->getURL()->fetch());
		}
		return $this->file;
	}

	/**
	 * URLを返す
	 *
	 * @access public
	 * @return BSURL browscap.iniのURL
	 */
	public function getURL () {
		if (!$this->url) {
			$this->url = new BSURL(BS_BROWSCAP_URL);
		}
		return $this->url;
	}

	/**
	 * マッチした属性名の配列を返す
	 *
	 * @access private
	 * @param string $useragent ユーザーエージェント
	 * @return string[] マッチした属性名
	 */
	private function getMatchedNames ($useragent) {
		$key = null;
		foreach ($this->getAttributes() as $current => $values) {
			if (preg_match($values['Pattern'], $useragent)) {
				if (strlen($key) < strlen($current)) {
					$key = $current;
				}
			}
		}

		$keys = array();
		do {
			$keys[] = $key;
			$values = $this->getAttribute($key);
		} while ($key = $values['Parent']);
		krsort($keys);
		return $keys;
	}
}

/* vim:set tabstop=4 ai: */
?>