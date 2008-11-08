<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent
 */

/**
 * browscap
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSBrowscap extends BSParameterHolder {
	private $url;
	private $file;
	static private $instance;

	/**
	 * @access private
	 */
	private function __construct () {
		if (!$this->isEnable()) {
			return;
		}
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
	 * @access public
	 */
	public function __clone () {
		throw new BSSingletonException('"%s"はコピー出来ません。', __CLASS__);
	}

	/**
	 * 利用可能か？
	 *
	 * @access public
	 * @return boolean 利用可能ならTrue
	 */
	public function isEnable () {
		$controller = BSController::getInstance();
		return $controller->isResolvable()
			&& $controller->getConstant('USERAGENT_BROWSCAP_ENABLE');
	}

	/**
	 * ユーザーエージェント情報を返す
	 *
	 * @access public
	 * @param string $useragent ユーザーエージェント名
	 * @return BSArray ユーザーエージェント情報
	 */
	public function getInfo ($useragent = null) {
		if (!$useragent) {
			$useragent = BSController::getInstance()->getEnvironment('HTTP_USER_AGENT');
		}

		$type = $this->getType($useragent);
		$name = sprintf(
			'%s.%s',
			get_class($this),
			BSString::pascalize(str_replace('.', '', $type))
		);
		$expire = $this->getFile()->getUpdateDate();
		if ($info = BSController::getInstance()->getAttribute($name, $expire)) {
			return new BSArray($info);
		} else {
			$info = new BSArray;
			foreach ($this->getMatchedKeys($type) as $key) {
				$info->setParameters($this[$key]);
			}
			BSController::getInstance()->setAttribute($name, $info->getParameters());
			return $info;
		}
	}

	/**
	 * タイプ名を返す
	 *
	 * @access public
	 * @param string $useragent ユーザーエージェント名
	 * @return string タイプ名
	 */
	public function getType ($useragent = null) {
		if (!$useragent) {
			$useragent = BSController::getInstance()->getEnvironment('HTTP_USER_AGENT');
		}

		$name = sprintf('%s.%s', get_class($this), __FUNCTION__);
		if (!$type = BSUser::getInstance()->getAttribute($name)) {
			$pattern = null;
			foreach ($this as $key => $values) {
				if (preg_match($values['Pattern'], $useragent)
					&& (strlen($pattern) < strlen($values['Pattern']))) {

					$type = $values['Parent'];
					$pattern = $values['Pattern'];
				}
			}
			BSUser::getInstance()->setAttribute($name, $type);
		}
		return $type;
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
			if (!$this->file = $dir->getEntry('browscap.ini', 'BSConfigFile')) {
				$this->file = $dir->createEntry('browscap.ini', 'BSConfigFile');
				$this->file->setContents($this->getURL()->fetch());
			}
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
			$config = array();
			require(BSConfigManager::getInstance()->compile('useragent/carrot'));
			$this->url = new BSURL($config['browscap']['url']);
		}
		return $this->url;
	}

	/**
	 * マッチした属性名の配列を返す
	 *
	 * @access private
	 * @param string $type タイプ名
	 * @return BSArray マッチした属性名
	 */
	private function getMatchedKeys ($type) {
		$keys = new BSArray;
		do {
			$keys->setParameter(null, $type, BSArray::POSITION_TOP);
			$values = $this[$type];
		} while ($type = $values['Parent']);
		return $keys;
	}
}

/* vim:set tabstop=4 ai: */
?>