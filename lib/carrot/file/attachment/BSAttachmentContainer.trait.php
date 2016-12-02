<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage file.attachment
 */

/**
 * 添付ファイルコンテナ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
trait BSAttachmentContainer {

	/**
	 * 添付ファイルの情報を返す
	 *
	 * @access public
	 * @param string $name 名前
	 * @return string[] 添付ファイルの情報
	 */
	public function getAttachmentInfo ($name = null) {
		if ($file = $this->getAttachment($name)) {
			$info = new BSArray;
			$info['path'] = $file->getPath();
			$info['size'] = $file->getSize();
			$info['type'] = $file->getType();
			$info['filename'] = $this->getAttachmentFileName($name);
			if ($url = $this->getAttachmentURL($name)) {
				$info['url'] = $url->getContents();
			}
			return $info;
		}
	}

	/**
	 * 添付ファイルを返す
	 *
	 * @access public
	 * @param string $name 名前
	 * @return BSFile 添付ファイル
	 * @abstract
	 */
	abstract public function getAttachment ($name = null);

	/**
	 * 添付ファイルを設定
	 *
	 * @access public
	 * @param BSFile $file 添付ファイル
	 * @param string $name 名前
	 * @abstract
	 */
	abstract public function setAttachment (BSFile $file, $name = null);

	/**
	 * 添付ファイルベース名を返す
	 *
	 * @access public
	 * @param string $name 名前
	 * @return string 添付ファイルベース名
	 */
	public function getAttachmentBaseName ($name) {
		return sprintf('%06d_%s', $this->getID(), $name);
	}

	/**
	 * 添付ファイルのダウンロード時の名を返す
	 *
	 * @access public
	 * @param string $name 名前
	 * @return string ダウンロード時ファイル名
	 */
	public function getAttachmentFileName ($name = null) {
		if ($file = $this->getAttachment($name)) {
			return $this->getAttachmentBaseName($name) . $file->getSuffix();
		}
	}

	/**
	 * 添付ファイルのURLを返す
	 *
	 * @access public
	 * @param string $name 名前
	 * @return BSURL 添付ファイルURL
	 */
	public function getAttachmentURL ($name = null) {
	}
}

/* vim:set tabstop=4: */
