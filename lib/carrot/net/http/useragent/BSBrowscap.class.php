<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent
 */

/**
 * browscap
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSBrowscap extends BSParameterHolder {
	private $url;
	private $file;
	static private $instance;

	/**
	 * コンストラクタ
	 *
	 * @access private
	 */
	private function __construct () {
		$expire = BSDate::getNow()->setAttribute('day', '-7');
		if ($params = BSController::getInstance()->getAttribute(get_class($this), $expire)) {
			$this->setParameters($params);
		}
		if (!$this->getParameters()) {
			foreach ($this->getFile()->getResult() as $key => $values) {
				if (!isset($values['Parent'])) {
					$values['Parent'] = null;
				}
				$pattern = preg_quote(strtolower($key), '/');
				$pattern = str_replace(array('\*', '\?'), array('.*', '.'), $pattern);
				$pattern = '/^' . $pattern . '/i';
				$values['Pattern'] = $pattern;
				$this[$key] = $values;
			}
			BSController::getInstance()->setAttribute(get_class($this), $this->getParameters());
		}
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
			self::$instance = new BSBrowscap;
		}
		return self::$instance;
	}

	/**
	 * ディープコピー
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
	 * @return BSArray ユーザーエージェント情報
	 */
	public function getInfo ($name = null) {
		if (!$name) {
			$name = BSController::getInstance()->getEnvironment('HTTP_USER_AGENT');
		}

		$info = new BSArray;
		$info['name'] = $name;
		foreach ($this->getMatchedNames($name) as $key) {
			$info->setParameters($this[$key]);
		}
		return $info;
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
			$this->url = new BSURL(BSController::getInstance()->getConstant('BROWSCAP_URL'));
		}
		return $this->url;
	}

	/**
	 * マッチした属性名の配列を返す
	 *
	 * @access private
	 * @param string $useragent ユーザーエージェント
	 * @return BSArray マッチした属性名
	 */
	private function getMatchedNames ($useragent) {
		$key = null;
		foreach ($this as $current => $values) {
			if (preg_match($values['Pattern'], $useragent)) {
				if (strlen($key) < strlen($current)) {
					$key = $current;
				}
			}
		}

		$keys = new BSArray;
		do {
			$keys->setParameter(null, $key, BSArray::POSITION_TOP);
			$values = $this[$key];
		} while ($key = $values['Parent']);
		return $keys;
	}
}

/* vim:set tabstop=4 ai: */
?>