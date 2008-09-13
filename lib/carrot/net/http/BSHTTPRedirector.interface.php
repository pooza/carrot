<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http
 */

/**
 * リダイレクト対象
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
interface BSHTTPRedirector {

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