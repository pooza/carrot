<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net.mime
 */

/**
 * MIMEタイプ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSMIMEType extends BSParameterHolder {
	use BSSingleton;
	private $suffixes;
	const DEFAULT_TYPE = 'application/octet-stream';

	/**
	 * @access protected
	 */
	protected function __construct () {
		$config = BSConfigManager::getInstance()->compile('mime');
		$this->setParameters($config['types']);
	}

	/**
	 * パラメータを返す
	 *
	 * @access public
	 * @param string $name パラメータ名
	 * @return mixed パラメータ
	 */
	public function getParameter ($name) {
		$name = '.' . ltrim($name, '.');
		$name = BSString::toLower($name);
		return parent::getParameter($name);
	}

	/**
	 * パラメータを設定
	 *
	 * @access public
	 * @param string $name パラメータ名
	 * @param mixed $value 値
	 */
	public function setParameter ($name, $value) {
		if (!BSString::isBlank($value)) {
			$name = '.' . ltrim($name, '.');
			$name = BSString::toLower($name);
			parent::setParameter($name, $value);
		}
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
			$this->suffixes = $types->createFlipped();
		}
		return $this->suffixes;
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
		if (BSString::isBlank($type = $types[BSMIMEUtility::getFileNameSuffix($suffix)])) {
			if ($flags & BSMIMEUtility::IGNORE_INVALID_TYPE) {
				$type = self::DEFAULT_TYPE;
			}
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
		$suffixes = self::getInstance()->getSuffixes();
		return $suffixes[$type];
	}
}

/* vim:set tabstop=4: */
