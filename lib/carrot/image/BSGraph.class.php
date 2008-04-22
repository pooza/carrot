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
 * @version $Id: BSGraph.class.php 140 2008-02-14 16:33:41Z pooza $
 */
class BSGraph extends PHPlot implements BSImageRenderer {
	private $width;
	private $height;
	private $type;
	private $error;
	private $min = 0;
	protected $img;
	protected $data;
	public $num_data_rows;
	public $plot_max_x;
	public $plot_min_x;
	public $plot_max_y;
	public $plot_min_y;
	public $plot_area;
	const DEFAULT_FONT = 'VL-PGothic-Regular';

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
		$this->setShading(2);

		$colors = array(
			'SlateBlue', 'green', 'peru', 'salmon', 'blue',
			'YellowGreen', 'beige', 'red', 'SkyBlue', 'yellow',
			'pink', 'cyan', 'orange', 'tan', 'maroon',
			'DarkGreen', 'gold', 'brown', 'magenta', 'plum',
		);
		$this->setDataColors($colors);
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
			$image->drawText($e->getMessage(), $image->getCoordinate(6, 18));
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
		if (!$this->num_data_rows) {
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

		if ($this->plot_type == 'pie') {
			$this->setDataType('text-data-single');
			$legends = array();
			foreach ($source as $row) {
				$row = array_values($row);
				$legends[] = BSString::truncate($row[0], 16);
				$values[] = $row;
			}
			$this->setLegend($legends);
		} else {
			$this->setDataType('text-data');
			foreach ($source as $row) {
				$row = array_values($row);
				$row[0] = BSString::truncate($row[0], 16);
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
			if ($this->plot_type != 'stackedbars') {
				$max = (ceil($max / $this->y_tick_inc) + 1) * $this->y_tick_inc;
				if (!is_null($this->min)) {
					$min = $this->min;
				} else {
					$min = (floor($min / $this->y_tick_inc) - 1) * $this->y_tick_inc;
				}
				$this->setPlotAreaWorld(0, $min, count($source), $max);
			}
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

	/**
	 * 円グラフ描画のオーバライド
	 *
	 * @access public
	 */
	public function drawPieChart() {
		$xpos = $this->plot_area[0] + $this->plot_area_width/2;
		if ($this->legend) {
			$ypos = $this->plot_area[1] + count($this->legend) * $this->legend_font['size'] * 2 + 240;
		} else {
			$ypos = $this->plot_area[1] + $this->plot_area_height/2;
		}
		$diameter = min($this->plot_area_width, $this->plot_area_height);
		$radius = $diameter/2;

		switch ($this->data_type) {
			case 'text-data':
				for ($i = 0; $i < $this->num_data_rows; $i++) {
					for ($j = 1; $j < $this->num_recs[$i]; $j++) {
						@ $sumarr[$j] += abs($this->data[$i][$j]);
					}
				}
				break;
			case 'text-data-single':
				for ($i = 0; $i < $this->num_data_rows; $i++) {
					$legend[$i] = $this->data[$i][0];
					$sumarr[$i] = $this->data[$i][1];
				}
				break;
			case 'data-data':
				for ($i = 0; $i < $this->num_data_rows; $i++) {
					for ($j = 2; $j < $this->num_recs[$i]; $j++) {
						@ $sumarr[$j] += abs($this->data[$i][$j]);
					}
				}
				break;
			default:
				throw new BSImageException('Data type "%s" not supported.', $this->data_type);
		}

		if (!$total = array_sum($sumarr)) {
			throw new BSImageException('Empty data set');
		}

		if ($this->shading) {
			$diam2 = $diameter / 2;
		} else {
			$diam2 = $diameter;
		}
		$max_data_colors = count($this->data_colors);

		for ($h = $this->shading; $h >= 0; $h--) {
			$color_index = 0;
			$start_angle = 0;
			$end_angle = 0;
			foreach ($sumarr as $val) {
				if ($h == 0) {
					$slicecol = $this->ndx_data_colors[$color_index];
				} else {
					$slicecol = $this->ndx_data_dark_colors[$color_index];
				}

				$val = 360 * ($val / $total);
				$start_angle = $end_angle;
				$end_angle += $val;
				$mid_angle = deg2rad($end_angle - ($val / 2));

				ImageFilledArc(
					$this->img, $xpos, $ypos+$h, $diameter*0.7, $diam2,
					360-$end_angle, 360-$start_angle, $slicecol, IMG_ARC_PIE
				);

				if ($h == 0) {
					if (!$this->shading) {
						ImageFilledArc(
							$this->img, $xpos, $ypos+$h, $diameter*0.7, $diam2,
							360-$end_angle, 360-$start_angle,
							$this->ndx_grid_color, IMG_ARC_PIE | IMG_ARC_EDGED |IMG_ARC_NOFILL
						);
					}

					if (2 <= ($val / $total * 100)) {
						$label_txt = number_format(($val / $total * 100), $this->y_precision, '.', ', ') . '%';
						$label_x = $xpos + ($diameter * 0.8 * cos($mid_angle)) * $this->label_scale_position;
						$label_y = $ypos+$h - ($diam2 * 1.1 * sin($mid_angle)) * $this->label_scale_position;

						$this->DrawText($this->generic_font, 0, $label_x, $label_y,
							$this->ndx_grid_color, $label_txt, 'center', 'center'
						);
					}
				}
				$color_index ++;
				$color_index = $color_index % $max_data_colors;
			}
		}
	}

	/**
	 * 積み上げ棒グラフ描画のオーバライド
	 *
	 * @access public
	 */
	public function drawStackedBars() {
		if ($this->data_type != 'text-data') {
			throw new BSImageException('Bar plots must be "text-data"');
		}

		$x_first_bar = $this->record_bar_width / 2 - $this->bar_adjust_gap;
		for ($row = 0; $row < $this->num_data_rows; $row++) {
			$x_now_pixels = $this->xtr(0.5 + $row);
			if ($this->x_data_label_pos != 'none') {
				$this->DrawXDataLabel($this->data[$row][0], $x_now_pixels);
			}

			$x1 = $x_now_pixels - $x_first_bar;
			$x2 = $x1 + $this->actual_bar_width;

			$oldv = 0;
			for ($record = $this->num_recs[$row] - 1 ; 0 < $record ; $record--) {
				if (is_numeric($this->data[$row][$record])) {
					$y1 = $this->ytr(abs($this->data[$row][$record]) + $oldv);
					$y2 = $this->ytr($this->x_axis_position + $oldv);
					$oldv += abs($this->data[$row][$record]);
					ImageFilledRectangle(
						$this->img, $x1, $y1, $x2, $y2, $this->ndx_data_colors[$record - 1]
					);
					if ($this->shading) {
						ImageFilledPolygon(
							$this->img,
							array(
								$x1, $y1,
								$x1 + $this->shading, $y1 - $this->shading,
								$x2 + $this->shading, $y1 - $this->shading,
								$x2 + $this->shading, $y2 - $this->shading,
								$x2, $y2,
								$x2, $y1
							),
							6, $this->ndx_data_dark_colors[$record - 1]
						);
					} else {
						ImageRectangle(
							$this->img, $x1, $y1, $x2,$y2,
							$this->ndx_data_border_colors[$record - 1]
						);
					}
				}
			}
		}
	}
}

/* vim:set tabstop=4 ai: */
?>