<?php
/**
 * @package org.carrot-framework
 * @subpackage smarttag
 */

/**
 * 絵文字タグ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSPictogramTag extends BSSmartTag {
	static private $agents;

	/**
	 * タグ名を返す
	 *
	 * @access public
	 * @return string タグ名
	 */
	public function getTagName () {
		return 'pictogram';
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
		$carrier = BSMobileCarrier::getInstance($this->tag[2]);
		$raw = $carrier->getPictogram($this->tag[1]);
		if ($this->isRawMode()) {
			$replace = $raw;
		} else {
			$carrier->getMPC()->setString($raw);
			$replace = $carrier->getMPC()->convert($this->tag[2], BSMobileCarrier::MPC_IMAGE);
		}
		return str_replace($this->contents, $replace, $body);
	}

	private function isRawMode () {
		$useragent = BSRequest::getInstance()->getUserAgent();
		$carrier = BSMobileCarrier::getInstance($this->tag[2]);
		return $useragent->isMobile() && ($carrier->getName() == $useragent->getType());
	}
}

/* vim:set tabstop=4: */
