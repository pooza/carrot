<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage image
 */

/**
 * 画像コンテナ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSImageContainer.interface.php 141 2008-02-16 10:38:42Z pooza $
 */
interface BSImageContainer {

	/**
	 * 画像の情報を返す
	 *
	 * @access public
	 * @param string $size サイズ (s|l)
	 * @return string[] 画像の情報
	 */
	public function getImageInfo ($size = null);

	/**
	 * 画像ファイルを返す
	 *
	 * @access public
	 * @param string $size サイズ (s|l)
	 * @return UoImageFile 画像ファイル
	 */
	public function getImageFile ($size = null);

	/**
	 * 画像ファイルを設定する
	 *
	 * @access public
	 * @param BSImageFile $file 画像ファイル
	 * @param string $size サイズ (s|l)
	 */
	public function setImageFile (BSImageFile $file, $size = null);

	/**
	 * 画像ファイルベース名を返す
	 *
	 * @access public
	 * @param string $size サイズ
	 * @return string 画像ファイルベース名
	 */
	public function getImageFileBaseName ($size = null);
}

/* vim:set tabstop=4 ai: */
?>