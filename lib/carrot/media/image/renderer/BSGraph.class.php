<?php
/**
 * @package org.carrot-framework
 * @subpackage media.image.renderer
 */

BSUtility::includeFile('phplot/phplot');

/**
 * グラフレンダラー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSGraph extends PHPlot implements BSImageRenderer {
	private $width;
	private $height;
	private $type;
	private $error;
	private $min = 0;
	protected $gd;
	protected $data;
	public $num_data_rows;
	public $plot_max_x;
	public $plot_min_x;
	public $plot_max_y;
	public $plot_min_y;
	public $plot_area;

	/**
	 * @access public
	 * @param integer $width プロットエリアの幅（ピクセル）
	 * @param integer $height プロットエリアの高さ（ピクセル）
	 */
	public function __construct ($width = 600, $height = 400) {
		$this->width = $width;
		$this->height = $height;
		$this->setType(BSMIMEType::getType('gif'));
		parent::PHPlot($width, $height);
		$this->setTTFPath(BSFileUtility::getPath('font'));
		$this->setDefaultTTFont(BSFontManager::DEFAULT_FONT);
		$this->setUseTTF(true);
		$this->setPlotType('lines');
		$this->setBackGroundColor('white');
		$this->setPlotBorderType('left');
		$this->setXLabelAngle(90);
		$this->setXTickLabelPos('none');
		$this->setXTickPos('none');
		$this->setShading(0);

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
			$this->gd = $image->getGDHandle();
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
	 * @param string $type メディアタイプ又は拡張子
	 */
	public function setType ($type) {
		if (!BSString::isBlank($suggested = BSMIMEType::getType($type, null))) {
			$type = $suggested;
		}
		if (BSString::isBlank($suffix = BSImage::getSuffixes()->getParameter($type))) {
			$message = new BSStringFormat('メディアタイプ"%s"が正しくありません。');
			$message[] = $type;
			throw new BSImageException($message);
		}
		$this->setFileFormat(str_replace('.', '', $suffix));
		$this->type = $type;
	}

	/**
	 * GD画像リソースを返す
	 *
	 * @access public
	 * @return resource GD画像リソース
	 */
	public function getGDHandle () {
		if (!is_resource($this->gd)) {
			throw new BSImageException('GD画像リソースが正しくありません。');
		}
		$this->getContents();
		return $this->gd;
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
	 * グラフデータ設定
	 *
	 * @access public
	 * @param integer[] $source グラフのデータ
	 */
	public function setDataValues ($source) {
		$values = array();

		if ($this->plot_type == 'pie') {
			$this->setDataType('text-data-single');

			// 総数を得る
			$total = 0;
			foreach ($source as $row) {
				$row = array_values($row);
				$total += $row[1];
			}

			// 
			$legends = array();
			$others = 0;
			foreach ($source as $row) {
				$row = array_values($row);
				if (($total / 100) < $row[1]) {
					$legends[] = BSString::truncate($row[0], 32);
					$values[] = $row;
				} else {
					$others += $row[1];
				}
			}
			if ($others) {
				$legends[] = '上記以外の項目';
				$values[] = array(null, $others);
			}
			$this->setLegend($legends);
		} else {
			$this->setDataType('text-data');
			foreach ($source as $row) {
				$row = array_values($row);
				$row[0] = BSString::split($row[0], 16);
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
				// 目盛の桁を算出
				$tick = 1;
				while (($tick * 10) < $max) {
					$tick = $tick * 10;
				}
				$this->setYTickIncrement($tick);

				// 最大値と最小値を算出
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
	 * デフォルトフォント設定
	 *
	 * @access public
	 * @param string $font フォント名
	 */
	public function setDefaultTTFont ($font) {
		if (!$file = BSFileUtility::getDirectory('font')->getEntry($font)) {
			$message = new BSStringFormat('フォントファイル名"%s"が正しくありません。');
			$message[] = $font;
			throw new BSImageException($message);
		}
		$this->default_ttfont = $file->getName();
		return $this->setDefaultFonts();
	}

	/**
	 * デフォルトフォント設定
	 *
	 * @access public
	 */
	public function setDefaultFonts () {
		$this->setFont('generic', $this->default_ttfont, 9);
		$this->setFont('title', $this->default_ttfont, 12);
		$this->setFont('legend', $this->default_ttfont, 9);
		$this->setFont('x_label', $this->default_ttfont, 9);
		$this->setFont('y_label', $this->default_ttfont, 9);
		$this->setFont('x_title', $this->default_ttfont, 9);
		$this->setFont('y_title', $this->default_ttfont, 9);
		return true;
	}

	/**
	 * 凡例描画
	 *
	 * @access public
	 * @param integer $which_x1 左上のX座標
	 * @param integer $which_71 左上のY座標
	 * @param string $which_boxtype 未使用
	 */
	public function drawLegend ($which_x1, $which_y1, $which_boxtype) {
		// Find maximum legend label length
		$max_len = 0;
		foreach ($this->legend as $leg) {
			$len = strlen($leg);
			$max_len = ($len > $max_len) ? $len : $max_len;
		}

		$size = $this->TTFBBoxSize($this->legend_font['size'], 0,
								   $this->legend_font['font'], 'H');
		$char_w = $size[0];

		$size = $this->TTFBBoxSize($this->legend_font['size'], 0,
								   $this->legend_font['font'], 'H');
		$char_h = $size[1];

		$v_margin = $char_h/2;						 // Between vertical borders and labels
		$dot_height = $char_h + $this->line_spacing;   // Height of the small colored boxes
		$width = $char_w * ($max_len + 4);

		//////// Calculate box size
		// upper Left
		if ( (! $which_x1) || (! $which_y1) ) {
			$box_start_x = $this->plot_area[2] - $width;
			$box_start_y = $this->plot_area[1] + 5;
		} else {
			$box_start_x = $which_x1;
			$box_start_y = $which_y1;
		}

		// Lower right corner
		$box_end_y = $box_start_y + $dot_height*(count($this->legend)) + 2*$v_margin;
		$box_end_x = $box_start_x + $width - 5;

		// Draw outer box
		//ImageFilledRectangle($this->gd, $box_start_x, $box_start_y, $box_end_x, $box_end_y, $this->ndx_bg_color);
		//ImageRectangle($this->gd, $box_start_x, $box_start_y, $box_end_x, $box_end_y, $this->ndx_grid_color);

		$color_index = 0;
		$max_color_index = count($this->ndx_data_colors) - 1;

		$dot_left_x = $box_end_x - $char_w * 2;
		$dot_right_x = $box_end_x - $char_w;
		$y_pos = $box_start_y + $v_margin;

		foreach ($this->legend as $leg) {
			// Text right aligned to the little box
			$this->DrawText($this->legend_font, 0, $dot_left_x - $char_w, $y_pos,
							$this->ndx_text_color, $leg, 'right');
			// Draw a box in the data color
			ImageFilledRectangle(
				$this->gd, $dot_left_x, $y_pos + 1, $dot_right_x, $y_pos + $dot_height-1,
				$this->ndx_data_colors[$color_index]
			);
			ImageRectangle(
				$this->gd, $dot_left_x, $y_pos, $dot_right_x, $y_pos + $dot_height,
				$this->ndx_text_color
			);

			$y_pos += $char_h + 8;

			$color_index++;
			if ($color_index > $max_color_index)
				$color_index = 0;
		}
	}

	/**
	 * 円グラフ描画
	 *
	 * @access public
	 */
	public function drawPieChart () {
		if ($this->data_type != 'text-data-single') {
			throw new BSImageException('Pie plots must be "text-data-single"');
		}

		$xpos = $this->plot_area[0] + $this->plot_area_height/2;
		$ypos = $this->plot_area[1] + $this->plot_area_height/2;
		$diameter = min($this->plot_area_width, $this->plot_area_height);
		$radius = $diameter/2;

		for ($i = 0; $i < $this->num_data_rows; $i++) {
			$legend[$i] = $this->data[$i][0];
			$sumarr[$i] = $this->data[$i][1];
		}

		if (!$total = array_sum($sumarr)) {
			$this->DrawError('DrawPieChart(): Empty data set');
			return FALSE;
		}

		$diam2 = $diameter;
		$max_data_colors = count ($this->data_colors);

		$color_index = 0;
		$start_angle = 90;
		$end_angle = 90;
		foreach ($sumarr as $val) {
			$slicecol = $this->ndx_data_colors[$color_index];
			$label_percentage = $val / $total * 100;
			$val = 360 * ($val / $total);

			$start_angle = $end_angle;
			$end_angle -= $val;
			$mid_angle = deg2rad($start_angle - ($val / 2));

			// 細い円弧は描画しない
			if (0.3 < $label_percentage) {
				ImageFilledArc(
					$this->gd,
					$xpos, $ypos,
					$diameter, $diam2,
					360-$start_angle, 360-$end_angle,
					$slicecol, IMG_ARC_PIE
				);
			}

			// Draw the outline
			ImageFilledArc(
				$this->gd,
				$xpos, $ypos,
				$diameter, $diam2,
				360-$start_angle, 360-$end_angle,
				$this->ndx_grid_color, IMG_ARC_PIE | IMG_ARC_EDGED |IMG_ARC_NOFILL
			);

			$label_x = $xpos + ($diameter * 0.9 * cos($mid_angle)) * $this->label_scale_position;
			$label_y = $ypos - ($diam2 * 0.9 * sin($mid_angle)) * $this->label_scale_position;

			if (2 < $label_percentage) {
				$label_txt = number_format($label_percentage, $this->y_precision, '.', ',') . '%';
				$this->DrawText(
					$this->generic_font, 0,
					$label_x, $label_y,
					$this->ndx_grid_color,
					$label_txt, 'center', 'center'
				);
			}
			$color_index++;
			$color_index = $color_index % $max_data_colors;
		}
	}

	/**
	 * 積み上げ棒グラフ描画
	 *
	 * @access public
	 */
	public function drawStackedBars () {
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
						$this->gd, $x1, $y1, $x2, $y2, $this->ndx_data_colors[$record - 1]
					);
					ImageRectangle(
						$this->gd, $x1, $y1, $x2,$y2,
						$this->ndx_data_border_colors[$record - 1]
					);
				}
			}
		}
	}

	public function findDataLimits () {
		parent::findDataLimits();
		$this->max_t = min(32, $this->max_t + 4);
		return true;
	}

	/**
	 * エラーテキスト表示
	 *
	 * @access public
	 * @param string $message エラーメッセージ
	 */
	public function printError ($message) {
		throw new BSImageException($message);
	}

	/**
	 * エラー画像表示
	 *
	 * @access public
	 * @param string $message エラーメッセージ
	 */
	public function drawError ($message) {
		throw new BSImageException($message);
	}
}

/* vim:set tabstop=4: */
