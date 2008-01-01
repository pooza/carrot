<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage image
 */

BSController::includeLegacy('/phplot/phplot.php');

/**
 * グラフレンダラー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSGraph extends PHPlot implements BSImageRenderer {
	private $width;
	private $height;
	private $type;
	private $error;
	protected $img;
	protected $data;
	protected $num_data_rows;
	const DEFAULT_FONT = 'wlmaru2004p';

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param integer $width プロットエリアの幅（ピクセル）
	 * @param integer $height プロットエリアの高さ（ピクセル）
	 */
	public function __construct ($width = 600, $height = 400) {
		$this->width = $width;
		$this->height = $height;
		$this->setType('image/gif');
		parent::PHPlot($width, $height);
		$this->setTTFPath(BSController::getInstance()->getPath('font'));
		$this->setDefaultTTFont(self::DEFAULT_FONT);
		$this->setUseTTF(true);
		$this->setPlotType('lines');
		$this->setBackGroundColor('white');
		$this->setPlotBorderType('left');
		$this->setXLabelAngle(90);
		$this->setXTickLabelPos('none');
		$this->setXTickPos('none');
		$this->setYTickIncrement(1);
	}

	/**
	 * 送信内容を返す
	 *
	 * @access public
	 * @return string 送信内容
	 */
	public function getContents () {
		try {
			ob_start();
			$this->drawGraph();
			$contents = ob_get_contents();
			ob_end_clean();
			return $contents;
		} catch (BSImageException $e) {
			ob_end_clean();
			$image = new BSImage($this->getWidth(), $this->getHeight());
			$image->setType($this->getType());
			$image->drawText($e->getMessage(), 6, 18);
			$this->img = $image->getImage();
			return $image->getContents();
		}
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
		return $this->type;
	}

	/**
	 * メディアタイプを設定
	 *
	 * @access public
	 * @param string $type メディアタイプ
	 */
	public function setType ($type) {
		$formats = array(
			'image/jpeg' => 'jpg',
			'image/gif' => 'gif',
			'image/png' => 'png',
		);

		if (!$format = $formats[$type]) {
			throw new BSImageException('メディアタイプ"%s"が正しくありません。', $type);
		}
		$this->setFileFormat($format);
		$this->type = $type;
	}

	/**
	 * GDイメージリソースを返す
	 *
	 * @access public
	 * @return resource GDイメージリソース
	 */
	public function getImage () {
		if (!$this->img) {
			throw new BSImageException('有効な画像リソースがありません。');
		}
		$this->getContents();
		return $this->img;
	}

	/**
	 * 幅を返す
	 *
	 * @access public
	 * @return integer 幅
	 */
	public function getWidth () {
		return $this->width;
	}

	/**
	 * 高さを返す
	 *
	 * @access public
	 * @return integer 高さ
	 */
	public function getHeight () {
		return $this->height;
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		if (!$this->num_data_row) {
			$this->error = 'グラフデータが未定義です。';
			return false;
		}
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
	 * グラフデータ設定のオーバライド
	 *
	 * @access public
	 * @param integer[] $source グラフのデータ
	 */
	public function setDataValues ($source) {
		$values = array();
		$source = BSString::convertEncoding($source, 'sjis');

		if ($this->plot_type == 'pie') {
			$this->setDataType('text-data-single');
			$legends = array();
			foreach ($source as $row) {
				$row = array_values($row);
				$legends[] = $row[0];
				$values[] = $row;
			}
			$this->setLegend($legends);
		} else {
			$this->setDataType('text-data');
			foreach ($source as $row) {
				$row = array_values($row);
				for ($i = 1 ; $i < count($row) ; $i ++) { //見出要素を除いてループ
					if ($value = $row[$i]) {
						if (!isset($max) || ($max < $value)) {
							$max = $value;
						}
						if (!isset($min) || ($value < $min)) {
							$min = $value;
						}
					}
				}
				$values[] = $row;
			}
			$max = (ceil($max / $this->y_tick_inc) + 1) * $this->y_tick_inc;
			$min = (floor($min / $this->y_tick_inc) - 1) * $this->y_tick_inc;
			$this->setPlotAreaWorld(0, $min, count($source), $max);
		}

		parent::setDataValues($values);
	}

	/**
	 * デフォルトフォント設定のオーバライド
	 *
	 * @access public
	 * @param string $font フォント名
	 */
	public function setDefaultTTFont ($font) {
		if (!$file = BSController::getInstance()->getDirectory('font')->getEntry($font)) {
			throw new BSImageException('フォントファイル名"%s"が正しくありません。', $font);
		}
		$this->default_ttfont = $file->getName();
		return $this->setDefaultFonts();
	}

	/**
	 * デフォルトフォント設定のオーバライド
	 *
	 * @access public
	 */
	public function setDefaultFonts () {
		if ($this->use_ttf) {
			$this->setFont('generic', $this->default_ttfont, 9);
			$this->setFont('title', $this->default_ttfont, 12);
			$this->setFont('legend', $this->default_ttfont, 9);
			$this->setFont('x_label', $this->default_ttfont, 9);
			$this->setFont('y_label', $this->default_ttfont, 9);
			$this->setFont('x_title', $this->default_ttfont, 9);
			$this->setFont('y_title', $this->default_ttfont, 9);
			return true;
		}
		return parent::setDefaultFonts();
    }

	/**
	 * エラーテキスト表示のオーバライド
	 *
	 * @access public
	 * @param string $message エラーメッセージ
	 */
	public function printError ($message) {
		throw new BSImageException($message);
	}

	/**
	 * エラー画像表示のオーバライド
	 *
	 * @access public
	 * @param string $message エラーメッセージ
	 */
	public function drawError ($message) {
		throw new BSImageException($message);
	}
}

/* vim:set tabstop=4 ai: */
?>