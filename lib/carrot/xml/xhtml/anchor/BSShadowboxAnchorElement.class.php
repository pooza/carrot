<?php
/**
 * @package org.carrot-framework
 * @subpackage xml.xhtml.anchor
 */

/**
 * Shadowboxへのリンク
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSShadowboxAnchorElement extends BSImageAnchorElement {
	private $group;
	private $width;
	private $height;

	/**
	 * グループ名を設定
	 *
	 * @access public
	 * @param string $group グループ名
	 */
	public function setImageGroup ($group) {
		$this->group = $group;
	}

	/**
	 * 幅を設定
	 *
	 * @access public
	 * @param string $width 幅
	 */
	public function setWidth ($width) {
		$this->width = $width;
	}

	/**
	 * 高さを設定
	 *
	 * @access public
	 * @param string $height 高さ
	 */
	public function setHeight ($height) {
		$this->height = $height;
	}

	/**
	 * URLを設定
	 *
	 * @access public
	 * @param mixed $url
	 */
	public function setURL ($url) {
		if ($url instanceof BSHTTPRedirector) {
			$url = $url->getURL();
		} else {
			$url = BSURL::create();
			if (!($url instanceof BSHTTPURL)) {
				throw new BSNetException('正しいURLではありません。');
			}
		}
		$url['query'] = null;

		parent::setURL($url);
	}

	/**
	 * 内容をXMLで返す
	 *
	 * @access public
	 * @return string XML要素
	 */
	public function getContents () {
		$rel = new BSArray;
		if (BSString::isBlank($this->group)) {
			$rel[] = 'shadowbox';
		} else {
			$rel[] = 'shadowbox[. $this->group .]';
		}
		if ($this->width && $this->height) {
			$rel[] = 'width=' . $this->width;
			$rel[] = 'height=' . $this->height;
		}
		$this->setAttribute('rel', $rel->join(';'));
		return parent::getContents();
	}
}

/* vim:set tabstop=4: */
