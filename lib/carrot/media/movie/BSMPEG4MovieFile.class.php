<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage media.movie
 */

/**
 * MPEG4動画ファイル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSMPEG4MovieFile extends BSMovieFile {

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('MPEG4動画ファイル "%s"', $this->getShortPath());
	}

	/**
	 * プレイヤーの高さを返す
	 *
	 * @access public
	 * @return integer プレイヤーの高さ
	 */
	public function getPlayerHeight () {
		return BS_MOVIE_MP4_PLAYER_HEIGHT;
	}
}

/* vim:set tabstop=4: */
