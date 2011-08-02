<?php
/**
 * @package org.carrot-framework
 * @subpackage platform
 */

/**
 * Darwinプラットフォーム
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSDarwinPlatform extends BSPlatform {

	/**
	 * プロセスのオーナーを返す
	 *
	 * @access public
	 * @return string プロセスオーナーのユーザー名
	 */
	public function getProcessOwner () {
		return ltrim(parent::getProcessOwner(), '_');
	}
}

/* vim:set tabstop=4: */
