<?php
/**
 * @package org.carrot-framework
 * @subpackage media.image
 */

/**
 * 色
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSColor extends BSParameterHolder {
	const DEFAULT_COLOR = 'black';

	/**
	 * @access public
	 * @param string $color HTML形式の色コード
	 */
	public function __construct ($color = null) {
		if (BSString::isBlank($color)) {
			$color = self::DEFAULT_COLOR;
		} else if (is_numeric($color)) {
			$color = sprintf('%06x', $color);
		}
		$this->setColor($color);
	}

	/**
	 * HTML形式の色コードを設定
	 *
	 * @access public
	 * @param string $color HTML形式の色コード
	 */
	public function setColor ($color) {
		$color = ltrim($color, '#');
		if (BSString::isBlank($color) || BSNumeric::isZero($color)) {
			$this['red'] = 0;
			$this['green'] = 0;
			$this['blue'] = 0;
		} else if (mb_ereg('^[[:xdigit:]]{6}$', $color)) {
			$this['red'] = hexdec($color[0] . $color[1]);
			$this['green'] = hexdec($color[2] . $color[3]);
			$this['blue'] = hexdec($color[4] . $color[5]);
		} else if (mb_ereg('^[[:xdigit:]]{3}$', $color)) {
			$this['red'] = hexdec($color[0] . $color[0]);
			$this['green'] = hexdec($color[1] . $color[1]);
			$this['blue'] = hexdec($color[2] . $color[2]);
		} else {
			$color = BSString::toLower($color);
			$colors = new BSArray(BSConfigManager::getInstance()->compile('color'));
			if (BSString::isBlank($code = $colors[$color])) {
				$message = new BSStringFormat('色 "%s" は正しくありません。');
				$message[] = $color;
				throw new BSImageException($message);
			}
			$this->setColor($code);
		}
	}

	/**
	 * HTML形式の色コードを返す
	 *
	 * "#" をつける。
	 *
	 * @access public
	 * @return string HTML形式の色コード
	 */
	public function getContents () {
		return '#' . $this->getCode();
	}

	/**
	 * HTML形式の色コードを返す
	 *
	 * "#" をつけない。
	 *
	 * @access public
	 * @return string HTML形式の色コード
	 */
	public function getCode () {
		return sprintf('%02x%02x%02x', $this['red'], $this['green'], $this['blue']);
	}
}

/* vim:set tabstop=4: */
