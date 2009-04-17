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
		try {
			$useragent = BSRequest::getInstance()->getUserAgent();
			if ($useragent->isMobile()) {
				$replace = $this->getPictogramEntity();
			} else {
				$replace = BSMobileCarrier::getInstance('Docomo')->convertPictogram(
					$this->getRawPictogram(),
					BSMobileCarrier::MPC_IMAGE
				);
			}
		} catch (Exception $e) {
			$replace = sprintf('[エラー: %s]', $e->getMessage());
		}
		return str_replace($this->getContents(), $replace, $body);
	}

	private function getRawPictogram () {
		$name = $this->tag[1];
		require(BSConfigManager::getInstance()->compile('pictogram'));
		if (preg_match('/^[0-9]+$/', $name) && isset($config['codes'][$name])) {
			$code = $name;
		} else if (isset($config['names'][$name]['Docomo'])) {
			$code = $config['names'][$name]['Docomo'];
		} else {
			throw new BSMobileException('絵文字 "%s" が見つかりません。', $name);
		}
		return BSMobileCarrier::getInstance('Docomo')->getPictogram($code);
	}

	private function getPictogramEntity () {
		$name = $this->tag[1];
		$carrier = BSRequest::getInstance()->getUserAgent()->getCarrier();
		require(BSConfigManager::getInstance()->compile('pictogram'));
		if (preg_match('/^[0-9]+$/', $name) && isset($config['codes'][$name])) {
			$code = $name;
		} else if (isset($config['names'][$name])) {
			if (isset($config['names'][$name][$carrier->getName()])) {
				$code = $config['names'][$name][$carrier->getName()];
			} else {
				$code = $config['names'][$name]['Docomo'];
			}
		} else {
			throw new BSMobileException('絵文字 "%s" が見つかりません。', $name);
		}
		return '&#' . $code . ';';
	}
}

/* vim:set tabstop=4: */
