<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net.url
 */

/**
 * リダイレクト対象
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
interface BSRedirector {

	/**
	 * リダイレクト対象
	 *
	 * @access public
	 * @return BSURL
	 */
	public function getURL ();
}

/* vim:set tabstop=4 ai: */
?>