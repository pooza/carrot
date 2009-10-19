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
		$command->setDirectory(BSController::getInstance()->getDirectory('ffmpeg'));
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
		$names = new BSArray(array(
			'flv' => BSMIMEType::getType('.flv'),
			'mov' => BSMIMEType::getType('.mov'),
			'm4a' => BSMIMEType::getType('.mov'),
			'h264' => BSMIMEType::getType('.mov'),
			'wmv1' => BSMIMEType::getType('.wmv'),
			'wmv2' => BSMIMEType::getType('.wmv'),
			'wmv3' => BSMIMEType::getType('.wmv'),
			'rv10' => BSMIMEType::getType('.rm'),
			'rv20' => BSMIMEType::getType('.rm'),
			'rv30' => BSMIMEType::getType('.rm'),
			'rv40' => BSMIMEType::getType('.rm'),
		));
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
		return BSFileUtility::search($file, 'BSMovieFile');
	}
}

/* vim:set tabstop=4: */
