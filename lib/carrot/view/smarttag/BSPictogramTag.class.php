<?php
/**
 * @package org.carrot-framework
 * @subpackage view.smarttag
 */

/**
 * 絵文字タグ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSPictogramTag extends BSSmartTag {

	/**
	 * タグ名を返す
	 *
	 * @access public
	 * @return string タグ名
	 */
	public function getTagName () {
		return 'picto';
	}

	/**
	 * 置換して返す
	 *
	 * @access public
	 * @param string $body 置換対象文字列
	 * @return string 置換された文字列
	 * @abstract
	 */
	public function execute ($body) {
		try {
			if (BSRequest::getInstance()->getUserAgent()->isMobile()) {
				$replace = $this->getNumericReference();
			} else {
				$replace = $this->getImgTag();
			}
		} catch (Exception $e) {
			$replace = sprintf('[エラー: %s]', $e->getMessage());
		}
		return str_replace($this->getContents(), $replace, $body);
	}

	private function getImgTag () {
		$tag = BSMobileCarrier::getInstance('Docomo')->convertPictogram(
			$this->tag[1],
			BSMobileCarrier::MPC_IMAGE
		);
		if (BSString::isBlank($tag)) {
			throw new BSMobileException('絵文字 "%s" が見つかりません。', $name);
		}
		return $tag;
	}

	private function getNumericReference () {
		$carrier = BSRequest::getInstance()->getUserAgent()->getCarrier();
		if (BSString::isBlank($code = $carrier->getPictogramCode($this->tag[1]))) {
			throw new BSMobileException('絵文字 "%s" が見つかりません。', $name);
		}
		return '&#' . $code . ';';
	}
}

/* vim:set tabstop=4: */
