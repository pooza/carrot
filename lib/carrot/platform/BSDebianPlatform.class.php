<?php
/**
 * @package org.carrot-framework
 * @subpackage platform
 */

/**
 * Debianプラットフォーム
 *
 * Ubuntu等を含む、Debian系。
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSDebianPlatform extends BSLinuxPlatform {

	/**
	 * ディレクトリを返す
	 *
	 * @access public
	 * @param string $name ディレクトリ名
	 * @return BSDirectory ディレクトリ
	 */
	public function getDirectory ($name) {
		$constants = new BSConstantHandler($name);
		foreach (array($this->getName(), 'linux', 'default') as $suffix) {
			if (!BSString::isBlank($path = $constants['dir_' . $suffix])) {
				return new BSDirectory($path);
			}
		}
	}
}

/* vim:set tabstop=4: */
