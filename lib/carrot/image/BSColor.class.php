<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage image
 */

/**
 * 色
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSColor extends BSParameterHolder {
	const DEFAULT_COLOR = '#ffffff';

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param string $color HTML形式の色コード
	 */
	public function __construct ($color = null) {
		if (!$color) {
			$color = self::DEFAULT_COLOR;
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
		$color = preg_replace('/^#/', '', $color);
		if (preg_match('/^[0-9a-f]{6}$/i', $color)) {
			$this['red'] = hexdec(substr($color, 0, 2));
			$this['green'] = hexdec(substr($color, 2, 2));
			$this['blue'] = hexdec(substr($color, 4, 2));
			return;
		} else if (preg_match('/^[0-9a-f]{3}$/i', $color)) {
			$this['red'] = hexdec($color[0] . $color[0]);
			$this['green'] = hexdec($color[1] . $color[1]);
			$this['blue'] = hexdec($color[2] . $color[2]);
			return;
		}

		$color = strtolower($color);
		$config = array();
		require(BSConfigManager::getInstance()->compile('image/color'));
		$colors = $config;
		if (isset($colors[$color])) {
			$this->setColor($colors[$color]);
		} else {
			throw new BSImageException('色 "%s" は正しくありません。', $color);
		}
	}

	/**
	 * HTML形式の色コードを返す
	 *
	 * @access public
	 * @return string HTML形式の色コード
	 */
	public function getContents () {
		return sprintf('#%x%x%x', $this['red'], $this['green'], $this['blue']);
	}
}

/* vim:set tabstop=4 ai: */
?>