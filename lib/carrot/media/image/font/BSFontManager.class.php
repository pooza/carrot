<?php
/**
 * @package org.carrot-framework
 * @subpackage media.image.font
 */

/**
 * フォントマネージャ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id: BSColor.class.php 573 2008-09-13 07:38:10Z pooza $
 */
class BSFontManager {
	use BSSingleton;
	private $config;
	private $fonts;
	const DEFAULT_FONT = 'VL-PGothic-Regular';
	const DEFAULT_FONT_SIZE = 9;
	const MINCHO_FONT = 'MSPMincho';
	const GOTHIC_FONT = 'MSPGothic';

	/**
	 * @access protected
	 */
	protected function __construct () {
		$configure = BSConfigManager::getInstance();
		$this->config = new BSArray;
		$this->config->setParameters($configure->compile('font/carrot'));
		$this->config->setParameters($configure->compile('font/application'));
		$this->fonts = new BSArray;
	}

	/**
	 * ディレクトリを返す
	 *
	 * @access public
	 * @return BSDirectory ディレクトリ
	 */
	public function getDirectory () {
		return BSFileUtility::getDirectory('font');
	}

	/**
	 * フォントを返す
	 *
	 * @access public
	 * @param string フォント名 $name
	 * @return BSFont フォント
	 */
	public function getFont ($name = self::DEFAULT_FONT) {
		if (!$this->fonts[$name]) {
			if (isset($this->config[$name])) {
				$this->fonts[$name] = new BSFont($name, $this->config[$name]);
			}
		}
		return $this->fonts[$name];
	}
}

/* vim:set tabstop=4: */
