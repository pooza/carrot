<?php
/**
 * @package org.carrot-framework
 * @subpackage image
 */

/**
 * レーダーチャートレンダラー
 *
 * // ビューの中で、以下の様に使用する。
 * $this->setRenderer(new BSRaderChart(480, 320));
 * $data = new BSArray(array(
 *   'キソ肉マソ' => 95,
 *   'テリーマソ' => 95,
 *   'ロビソマスク' => 95,
 *   'ラーメソマソ' => 97,
 *   'ウォーズマソ' => 100,
 * ));
 * $this->getRenderer()->setData($data);
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @link http://www.rakuto.net/study/htdocs/ 参考
 */
class BSRaderChart extends BSImage {
	private $chartSize;
	private $origin;
	private $theta;
	private $data;
	private $drawed = false;

	/**
	 * @access public
	 * @param integer $width 幅
	 * @param integer $height 高さ
	 */
	public function __construct ($width, $height) {
		parent::__construct($width, $height);
		$this->chartSize = min($width, $height) / 2 - 16;
		$this->origin = $this->getCoordinate($width / 2, $height / 2);
	}

	/**
	 * データを設定する
	 *
	 * @access public
	 * @param BSArray $data データ
	 */
	public function setData (BSArray $data) {
		$this->data = $data;
		$this->theta = 360 / $this->data->count();
		$this->drawed = false;
	}

	/**
	 * カーソル座標を設定し、BSCoordinate座標を返す
	 *
	 * プロットエリア中心を原点とする為、素のBSCoordinateと座標系が異なる。
	 *
	 * @access private
	 * @param integer $x X座標
	 * @param integer $y Y座標
	 * @return BSCoordinate カーソル座標
	 */
	private function getCursor ($x, $y) {
		$coord = clone $this->origin;
		return $coord->move($x, $y);
	}

	/**
	 * 外枠を描く
	 *
	 * @access private
	 */
	private function drawPlotBorder () {
		$coords = new BSArray;		
		$angle = 0;
		$color = new BSColor('black');//100,100,100
		foreach ($this->data as $key => $value){
			$cursor = $this->getCursor(0, $this->chartSize * -1)->rotate($this->origin, $angle);
			$coords[] = clone $cursor;

			if ($cursor->getX() < $this->origin->getX()) {
				$cursor->move(strlen($key) * $this->getFontSize() / 2 * -1, 0);
			} else if ($this->origin->getX() == $cursor->getX()) {
				$cursor->move(strlen($key) * $this->getFontSize() / 4 * -1, 0);
			}
			if ($this->origin->getY() < $cursor->getY()) {
				$cursor->move(0, $this->getFontSize());
			}
			$this->drawText($key, $cursor, $color);

			$angle += $this->theta;
		}
		$this->drawPolygon($coords, $color);
	}

	/**
	 * 軸線を描く
	 *
	 * @access private
	 */
	private function drawRadiation () {
		$angle = 0;
		$color = new BSColor('black');//200,200,200
		foreach ($this->data as $row){
			$this->drawLine(
				$this->origin,
				$this->getCursor(0, $this->chartSize * -1)->rotate($this->origin, $angle),
				$color
			);
			$angle += $this->theta;
		}
	}

	/**
	 * 軸ラベルを描く
	 *
	 * @access private
	 */
	private function drawGridNumber () {
		$color = new BSColor('gray');
		foreach (array(0, 50, 100) as $label) {
			$this->drawText(
				$label,
				$this->getCursor(
					strlen($label) * $this->getFontSize() * -0.9, //フォントによる微調整必要？
					$this->chartSize * $label / 100 * -1 + $this->getFontSize()
				),
				$color
			);
		}
	}

	/**
	 * レーダーチャートを描く
	 *
	 * @access private
	 */
	private function drawRadar () {
		$angle   = 0;
		$coords = new BSArray;
		foreach ($this->data as $key => $value){
			$coords[] = $this->getCursor(0, $value * -1)->rotate($this->origin, $angle);
			$angle += $this->theta;
		}
		$this->drawPolygon($coords, new BSColor('yellow'), BSImage::FILLED); //200,0,0
		$this->drawPolygon($coords, new BSColor('gray')); //255,100,100
	}

	/**
	 * 描画
	 *
	 * @access public
	 */
	public function draw () {
		if (!$this->drawed) {
			$this->fill($this->getCoordinate(0, 0), new BSColor('white'));
			$this->drawPlotBorder();
			$this->drawRadar();
			$this->drawRadiation();
			$this->drawGridNumber();
			$this->drawed = true;
		}
	}

	/**
	 * 送信内容を返す
	 *
	 * @access public
	 * @return string 送信内容
	 */
	public function getContents () {
		if (!$this->drawed) {
			$this->draw();
		}
		return parent::getContents();
	}
}

/* vim:set tabstop=4 ai: */
?>