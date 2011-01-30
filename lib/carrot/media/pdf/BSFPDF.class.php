<?php
/**
 * @package org.carrot-framework
 * @subpackage media.pdf
 */

BSUtility::includeFile('fpdf/fpdf');
BSUtility::includeFile('fpdf/font/mbttfdef');
BSUtility::includeFile('fpdf/mbfpdf');

/**
 * MBFPDFラッパー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSFPDF extends MBFPDF implements BSRenderer {
	private $error;
	const PORTRAIT = 'P';
	const LANDSCAPE = 'L';
	const BOLD = 'B';
	const ITALIC = 'I';
	const BOLD_ITALIC = 'BI';
	const CENTER = 'C';
	const RIGHT = 'R';
	const LEFT = 'L';

	/**
	 * @access public
	 * @param string $orientation 用紙の向き
	 * @param string $unit 単位
	 * @param string $format 用紙のサイズ
	 */
	public function __construct ($orientation = self::PORTRAIT, $unit = 'mm', $format = 'A4') {
		$constants = BSConstantHandler::getInstance();
		if (!$constants->hasParameter('FPDF_FONTPATH')) {
			$constants['FPDF_FONTPATH'] =  BS_LIB_DIR . '/fpdf/font/';
		}
		parent::FPDF($orientation, $unit, $format);
		$this->open();
	}

	/**
	 * 送信内容を返す
	 *
	 * @access public
	 * @return string 送信内容
	 */
	public function getContents () {
		ob_start();
		$this->output();
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}

	/**
	 * 出力内容のサイズを返す
	 *
	 * @access public
	 * @return integer サイズ
	 */
	public function getSize () {
		return strlen($this->getContents());
	}

	/**
	 * メディアタイプを返す
	 *
	 * @access public
	 * @return string メディアタイプ
	 */
	public function getType () {
		return BSMIMEType::getType('pdf');
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		return true;
	}

	/**
	 * エラーメッセージを返す
	 *
	 * @access public
	 * @return string エラーメッセージ
	 */
	public function getError () {
		return $this->error;
	}

	/**
	 * エラーハンドラのオーバライド
	 *
	 * @access public
	 * @param string $str エラーメッセージ
	 */
	public function error ($str) {
		throw new BSPDFException($str);
	}

	/**
	 * カレントフォントを設定
	 *
	 * @access public
	 * @param string $family フォント名
	 * @param string $style フォントスタイル
	 * @param integer $size フォントサイズ
	 * @param integer $hs 
	 */
	public function setFont ($family, $style = null, $size = 12, $hs = 100) {
		if (!isset($this->CoreFonts[BSString::toLower($family)])
			&& !isset($this->fonts[BSString::toLower($family)])) {
			$this->addMBFont($family, 'SJIS');
		}
		parent::setFont($family, $style, $size, $hs);
	}

	/**
	 * 文字列を出力
	 *
	 * @access public
	 * @param string $str 文字列
	 * @param float $height 行高（mm指定）
	 */
	public function putLine ($str, $height = null) {
		// 行高の指定がない場合は、1.5行と
		if (!$height) {
			$height = $this->FontSizePt / $this->k * 1.5;
		}

		$this->write($height, $str);
		$this->ln();
	}

	/**
	 * ヘッダのフォントを返す
	 *
	 * @access public
	 * @return string フォント名
	 */
	protected function getHeaderFont () {
		return self::GOTHIC_FONT;
	}

	/**
	 * フッタのフォントを返す
	 *
	 * @access public
	 * @return string フォント名
	 */
	protected function getFooterFont () {
		return self::GOTHIC_FONT;
	}

	/**
	 * ヘッダの内容を返す
	 *
	 * @access public
	 * @return string ヘッダの内容
	 */
	protected function getHeaderContents () {
		return BSController::getInstance()->getName();
	}

	/**
	 * フッタの内容を返す
	 *
	 * @access public
	 * @return string フッタの内容
	 */
	protected function getFooterContents () {
		return 'Page ' . $this->pageNo();
	}

	/**
	 * ヘッダを出力
	 *
	 * @access public
	 */
	public function header () {
		$this->setFont($this->getHeaderFont());
		$w = $this->getStringWidth($this->getHeaderContents()) + 6;
		$this->setX(($this->w - $w) / 2);
		$this->cell($w, 10, $this->getHeaderContents(), 0, 0, self::CENTER);
		$this->ln(10);
	}

	/**
	 * フッタを出力
	 *
	 * @access public
	 */
	public function footer () {
		$this->setY(-15);
		$this->setFont($this->getFooterFont(), null, 10);
		$this->cell(0, 10, $this->getFooterContents(), 0, 0, self::CENTER);
	}
}

/* vim:set tabstop=4: */
