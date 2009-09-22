<?php
/**
 * @package org.carrot-framework
 * @subpackage view.renderer.document_set
 */

/**
 * 書類セットエントリー
 *
 * BSDocumentSetにregister出来るクラスのインターフェース。
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
interface BSDocumentSetEntry extends BSTextRenderer {

	/**
	 * 内容を最適化して返す
	 *
	 * @access public
	 * @return string 最適化された内容
	 */
	public function getOptimizedContents ();
}

/* vim:set tabstop=4: */
