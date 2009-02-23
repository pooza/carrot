<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mail.mime.header
 */

/**
 * Content-Typeメールヘッダ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSContentTypeMailHeader extends BSMailHeader {

	/**
	 * 内容を設定
	 *
	 * @access public
	 * @param mixed $contents 内容
	 */
	public function setContents ($contents) {
		if ($contents instanceof BSRenderer) {
			$this->contents = self::getContentType($contents);
		} else {
			$this->contents = $contents;
		}

		$pattern = '/^mixed\/multipart; *boundary=\\"([.*]+)\\"/i';
		if (preg_match($pattern, $this->contents, $matches)) {
			$this->part->setBoundary($matches[1]);
		}
	}

	/**
	 * レンダラーの完全なタイプを返す
	 *
	 * @access public
	 * @param BSRenderer $renderer 対象レンダラー
	 * @return string メディアタイプ
	 * @static
	 */
	static public function getContentType (BSRenderer $renderer) {
		if ($renderer instanceof BSTextRenderer) {
			$encoding = $renderer->getEncoding();
			if (BSString::isBlank($charset = mb_preferred_mime_name($encoding))) {
				throw new BSMIMEException('エンコード"%s"が正しくありません。', $encoding);
			}
			return sprintf('%s; charset=%s', $renderer->getType(), $charset);
		}
		return $renderer->getType();
	}
}

/* vim:set tabstop=4: */
