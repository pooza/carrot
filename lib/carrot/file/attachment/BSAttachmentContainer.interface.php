<?php
/**
 * @package org.carrot-framework
 * @subpackage image.attachment
 */

/**
 * 添付ファイルコンテナ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
interface BSAttachmentContainer {

	/**
	 * 添付ファイルの情報を返す
	 *
	 * @access public
	 * @param string $name 名前
	 * @return string[] 添付ファイルの情報
	 */
	public function getAttachmentInfo ($name = null);

	/**
	 * 添付ファイルを返す
	 *
	 * @access public
	 * @param string $name 名前
	 * @return BSFile 添付ファイル
	 */
	public function getAttachment ($name = null);

	/**
	 * 添付ファイルを設定
	 *
	 * @access public
	 * @param BSFile $file 添付ファイル
	 * @param string $filename 添付ファイルの名前
	 * @param string $name 名前
	 */
	public function setAttachment (BSFile $file, $filename, $name = null);

	/**
	 * 添付ファイルベース名を返す
	 *
	 * @access public
	 * @param string $name 名前
	 * @return string 添付ファイルベース名
	 */
	public function getAttachmentBaseName ($name = null);
}

/* vim:set tabstop=4: */
