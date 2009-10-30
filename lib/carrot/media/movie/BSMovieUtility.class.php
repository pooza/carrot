<?php
/**
 * @package org.carrot-framework
 * @subpackage media.movie
 */

/**
 * 動画ユーティリティ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSMovieUtility {

	/**
	 * @access private
	 */
	private function __construct () {
	}

	/**
	 * コマンドラインを返す
	 *
	 * @access public
	 * @return BSCommandLine コマンドライン
	 * @static
	 */
	static public function getCommandLine () {
		$command = new BSCommandLine('bin/ffmpeg');
		$command->setDirectory(BSFileUtility::getDirectory('ffmpeg'));
		return $command;
	}

	/**
	 * MIMEタイプを返す
	 *
	 * @access public
	 * @param string $name FFmpegのエンコード名等
	 * @return string MIMEタイプ
	 * @static
	 */
	static public function getType ($name) {
		require(BSConfigManager::getInstance()->compile('movie_format'));
		$names = new BSArray($config['types']);
		return $names[BSString::toLower($name)];
	}

	/**
	 * 動画ファイルを返す
	 *
	 * @access public
	 * @param mixed パラメータ配列、BSFile、ファイルパス文字列
	 * @return BSMovieFile 動画ファイル
	 * @static
	 */
	static public function getFile ($file) {
		return BSMediaFile::search($file, 'BSMovieFile');
	}
}

/* vim:set tabstop=4: */
