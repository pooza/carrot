<?php
/**
 * @package org.carrot-framework
 * @subpackage media.image.renderer
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
 * @link http://www.rakuto.net/study/htdocs/ 参考
 */
class BSRaderChart extends BSImage {
	private $chartSize;
	private $origin;
	private $theta;
	private $data;
	private $max = 100;
	private $drawed = false;

	/**
	 * @access public
	 * @param integer $width 幅
	 * @param integer $height 高さ
	 */
	public function __construct ($width, $height) {
		parent::__construct($width, $height);
		$this->chartSize = min($width, $height) / 2 - 20;
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
	 * 最大値を設定
	 *
	 * @access public
	 * @param integer $max 最大値
	 */
	public function setMax ($max) {
		$this->max = $max;
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
	 * 色を返す
	 *
	 * @access private
	 * @param string $name 領域の名前
	 * @return BSColor 色
	 */
	private function getColor ($name) {
		$config = new BSArray(BSConfigManager::getInstance()->compile('rader_chart'));
		if (!$config->hasParameter($name)) {
			$message = new BSStringFormat('レーダーチャートの色 "%s" は未定義です。');
			$message[] = $name;
			throw new BSImageException($message);
		}
		return new BSColor($config[$name]);
	}

	/**
	 * 外枠を描く
	 *
	 * @access private
	 */
	private function drawBorder () {
		$coords = new BSArray;
		$angle = 0;
		foreach ($this->data as $key => $value){
			$cursor = $this->getCursor(0, $this->chartSize * -1)->rotate($this->origin, $angle);
			$coords[] = clone $cursor;

			$charWidth =  $this->getFontSize() / 2 * $this->getFont()->getParameter('aspect');
			if ($cursor->getX() < $this->origin->getX()) {
				$cursor->move(strlen($key) * $charWidth * -1, 0);
			} else if ($this->origin->getX() == $cursor->getX()) {
				$cursor->move(strlen($key) * $charWidth * -0.5, 0);
			}
			if ($this->origin->getY() < $cursor->getY()) {
				$cursor->move(0, $this->getFontSize());
			}
			$this->drawText($key, $cursor, $this->getColor('item_label'));

			$angle += $this->theta;
		}
		$this->drawPolygon($coords, $this->getColor('chart_border'));
	}

	/**
	 * 軸線を描く
	 *
	 * @access private
	 */
	private function drawRadiation () {
		$angle = 0;
		foreach ($this->data as $row){
			$this->drawLine(
				$this->origin,
				$this->getCursor(0, $this->chartSize * -1)->rotate($this->origin, $angle),
				$this->getColor('radiation')
			);
			$angle += $this->theta;
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
			$pixel = ($this->chartSize / $this->max) * $value;
			$coords[] = $this->getCursor(0, $pixel * -1)->rotate($this->origin, $angle);
			$angle += $this->theta;
		}
		$this->drawPolygon($coords, $this->getColor('rader_fill'), BSImage::FILLED);
		$this->drawPolygon($coords, $this->getColor('rader_border'));
	}

	/**
	 * 描画
	 *
	 * @access public
	 */
	public function draw () {
		if (!$this->drawed) {
			$this->fill($this->getCoordinate(0, 0), $this->getColor('background'));
			$this->drawBorder();
			$this->drawRadar();
			$this->drawRadiation();
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

/* vim:set tabstop=4: */
