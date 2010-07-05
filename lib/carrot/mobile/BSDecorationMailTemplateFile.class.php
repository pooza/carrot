<?php
/**
 * @package org.carrot-framework
 * @subpackage mobile
 */

/**
 * デコメールテンプレートファイル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSDecorationMailTemplateFile extends BSFile {
	private $type;

	/**
	 * バイナリファイルか？
	 *
	 * @access public
	 * @return boolean バイナリファイルならTrue
	 */
	public function isBinary () {
		return false;
	}

	/**
	 * メディアタイプを返す
	 *
	 * @access public
	 * @return string メディアタイプ
	 */
	public function getType () {
		return $this->type;
	}

	/**
	 * メディアタイプを設定
	 *
	 * @access public
	 * @param string $type メディアタイプ
	 */
	public function setType ($type) {
		$types = new BSArray;
		foreach (BSMobileCarrier::getNames() as $name) {
			$types[] = BSMobileCarrier::getInstance($name)->getDecorationMailType();
		}

		if (!$types->isContain($type)) {
			$message = new BSStringFormat('MIMEタイプ "%s" は、正しくありません。');
			$message[] = $type;
			throw new BSMobileException($message);
		}
		$this->type = $type;
	}
}

/* vim:set tabstop=4: */
