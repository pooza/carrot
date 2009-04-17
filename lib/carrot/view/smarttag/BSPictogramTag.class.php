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
	private $carrier;
	private $pictogram;
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
		try {
			$raw = $this->getRawPictogram();
			$useragent = BSRequest::getInstance()->getUserAgent();
			if ($useragent->isMobile()) {
				$useragent->getCarrier()->getMPC()->setFrom($this->getCarrier()->getMPCCode());
				$replace = $useragent->getCarrier()->convertPictogram(
					$raw,
					BSMobileCarrier::MPC_RAW
				);
			} else {
				$replace = $this->getCarrier()->convertPictogram(
					$raw,
					BSMobileCarrier::MPC_IMAGE
				);
			}
		} catch (Exception $e) {
			$replace = sprintf('[エラー: %s]', $e->getMessage());
		}
		return str_replace($this->getContents(), $replace, $body);
	}

	private function getCarrier () {
		if (!$this->carrier) {
			if (BSString::isBlank($this->tag[2])) {
				$this->tag[2] = 'Docomo';
			}
			$this->carrier = BSMobileCarrier::getInstance($this->tag[2]);
		}
		return $this->carrier;
	}

	private function getRawPictogram () {
		if (!$this->pictogram) {
			$code = $this->tag[1];
			if (preg_match('/^[0-9a-f]+$/i', $code)) {
				$code = hexdec($code);
			} else if (!preg_match('/^[0-9]+$/', $code)) {
				require(BSConfigManager::getInstance()->compile('pictogram'));
				if (!isset($config[$code])) {
					throw new BSMobileException('絵文字 "%s" が見つかりません。', $code);
				}
				$code = $config[$code];
			}
			$this->pictogram = $this->getCarrier()->getPictogram($code);
		}
		return $this->pictogram;
	}
}

/* vim:set tabstop=4: */
