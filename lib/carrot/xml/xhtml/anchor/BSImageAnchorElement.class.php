<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage xml.xhtml.anchor
 */

/**
 * 画像へのリンク
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @abstract
 */
abstract class BSImageAnchorElement extends BSAnchorElement {

	/**
	 * リンク対象画像を設定
	 *
	 * @access public
	 * @param BSImageContainer $record 対象レコード
	 * @param string $size サイズ名
	 * @param integer $pixel ピクセル数
	 * @param integer $flags フラグのビット列
	 *   self::WIDTH_FIXED 幅固定
	 *   self::HEIGHT_FIXED 高さ固定
	 *   self::WITHOUT_SQUARE 正方形に整形しない
	 *   self::FORCE_GIF gif形式を強制
	 * @return BSURL URL
	 */
	public function setImage (BSImageContainer $record, $size, $pixel = null, $flags = 0) {
		$images = $this->getUserAgent()->createImageManager($flags);
		$this->setURL($images->createURL($record, $size, $pixel));
	}
}

