<?php
/**
 * @package org.carrot-framework
 * @subpackage mobile
 */

/**
 * デコメールテンプレートファイル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
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
		if (!self::getTypes()->isContain($type)) {
			$message = new BSStringFormat('MIMEタイプ "%s" は、正しくありません。');
			$message[] = $type;
			throw new BSMobileException($message);
		}
		$this->type = $type;
	}

	/**
	 * 利用可能なメディアタイプを返す
	 *
	 * @access public
	 * @return BSArray メディアタイプ
	 */
	static public function getTypes () {
		$types = new BSArray;
		$suffixes = BSMIMEType::getInstance()->getSuffixes();
		foreach (BSMobileCarrier::getNames() as $name) {
			$type = BSMobileCarrier::getInstance($name)->getDecorationMailType();
			$types[$suffixes[$type]] = $type;
		}
		return $types;
	}
}

/* vim:set tabstop=4: */
