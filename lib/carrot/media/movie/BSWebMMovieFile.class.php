<?php
/**
 * @package org.carrot-framework
 * @subpackage media.movie
 */

/**
 * WebM動画ファイル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSWebMMovieFile extends BSMovieFile {

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('WebM動画ファイル "%s"', $this->getShortPath());
	}
}

/* vim:set tabstop=4: */
