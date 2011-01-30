<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mime
 */

/**
 * MIMEタイプ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSMIMEType extends BSParameterHolder implements BSSerializable {
	private $suffixes;
	static private $instance;
	const DEFAULT_TYPE = 'application/octet-stream';

	/**
	 * @access private
	 */
	private function __construct () {
		if (!$this->getSerialized()) {
			$this->serialize();
		}
		$this->setParameters($this->getSerialized());
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSMIMEType インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (!self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * @access public
	 */
	public function __clone () {
		throw new BadFunctionCallException(__CLASS__ . 'はコピーできません。');
	}

	/**
	 * パラメータを返す
	 *
	 * @access public
	 * @param string $name パラメータ名
	 * @return mixed パラメータ
	 */
	public function getParameter ($name) {
		return parent::getParameter(ltrim($name, '.'));
	}

	/**
	 * 属性名へシリアライズ
	 *
	 * @access public
	 * @return string 属性名
	 */
	public function serializeName () {
		return get_class($this);
	}

	/**
	 * シリアライズ
	 *
	 * @access public
	 */
	public function serialize () {
		$config = BSConfigManager::getInstance()->compile('mime');
		foreach ($config['types'] as $key => $value) {
			if (BSString::isBlank($value)) {
				$this->removeParameter($key);
			} else {
				$this[BSString::toLower($key)] = $value;
			}
		}
		BSController::getInstance()->setAttribute($this, $this->getParameters());
	}

	/**
	 * シリアライズ時の値を返す
	 *
	 * @access public
	 * @return mixed シリアライズ時の値
	 */
	public function getSerialized () {
		return BSController::getInstance()->getAttribute(
			$this,
			BSConfigManager::getConfigFile('mime')->getUpdateDate()
		);
	}

	/**
	 * 全てのサフィックスを返す
	 *
	 * @access public
	 * @return BSArray 全てのサフィックス
	 */
	public function getSuffixes () {
		if (!$this->suffixes) {
			$types = new BSArray($this->params);
			$this->suffixes = $types->getFlipped();
			$config = BSConfigManager::getInstance()->compile('mime');
			$this->suffixes->setParameters($config['suffixes']);
			foreach ($this->suffixes as $type => $suffix) {
				$this->suffixes[$type] = '.' . $suffix;
			}
		}
		return $this->suffixes;
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return 'MIMEタイプ';
	}

	/**
	 * アップロード可能なメディアタイプを返す
	 *
	 * @access public
	 * @return BSArray メディアタイプの配列
	 * @static
	 */
	static public function getAttachableTypes () {
		$types = new BSArray;
		$config = BSConfigManager::getInstance()->compile('mime');
		foreach ($config['types'] as $key => $value) {
			if ($key && $value) {
				$types['.' . $key] = $value;
			}
		}
		return $types;
	}

	/**
	 * アップロード可能なサフィックスを返す
	 *
	 * @access public
	 * @return BSArray サフィックスの配列
	 * @static
	 */
	static public function getAttachableSuffixes () {
		$suffixes = self::getAttachableTypes()->getFlipped();
		$config = BSConfigManager::getInstance()->compile('mime');
		foreach ($config['suffixes'] as $key => $value) {
			if ($key && $value) {
				$suffixes[$key] = '.' . $value;
			}
		}
		return $suffixes;
	}

	/**
	 * 規定のメディアタイプを返す
	 *
	 * @access public
	 * @param string $suffix サフィックス、又はファイル名
	 * @param integer $flags フラグのビット列
	 *   BSMIMEUtility::IGNORE_INVALID_TYPE タイプが不正ならapplication/octet-streamを返す
	 * @return string メディアタイプ
	 * @static
	 */
	static public function getType ($suffix, $flags = BSMIMEUtility::IGNORE_INVALID_TYPE) {
		$types = self::getInstance();
		if (BSString::isBlank($type = $types[BSMIMEUtility::getFileNameSuffix($suffix)])
			&& ($flags & BSMIMEUtility::IGNORE_INVALID_TYPE)) {
			$type = self::DEFAULT_TYPE;
		}
		return $type;
	}

	/**
	 * 規定のサフィックスを返す
	 *
	 * @access public
	 * @param string $type MIMEタイプ
	 * @return string サフィックス
	 * @static
	 */
	static public function getSuffix ($type) {
		return self::getInstance()->getSuffixes()->getParameter($type);
	}
}

/* vim:set tabstop=4: */
