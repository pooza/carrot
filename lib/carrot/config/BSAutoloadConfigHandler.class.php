<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage config
 */

/**
 * AutoloadConfigHandlerのラッパー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSAutoloadConfigHandler extends AutoloadConfigHandler {
	public static function & replaceConstants ($value) {
		return BSDirectoryFinder::replaceConstants($value);
	}
}

/* vim:set tabstop=4 ai: */
?>