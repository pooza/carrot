<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage config
 */

/**
 * DefineConfigHandlerのラッパー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSDefineConfigHandler.class.php 333 2007-06-08 05:48:46Z pooza $
 */
class BSDefineConfigHandler extends DefineConfigHandler {
	public static function & replaceConstants ($value) {
		return BSDirectoryFinder::replaceConstants($value);
	}
}

/* vim:set tabstop=4 ai: */
?>