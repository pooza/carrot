<?php
/**
 * @package org.carrot-framework
 * @subpackage numeric
 */

/**
 * 消費税計算機
 *
 * //当時100円のドクターペッパーが、1989/4/1より103円になったことを確認。
 * $tax = BSConsumptionTaxHandler::getInstance();
 * $price = $tax->includeTax(100, new BSDate(19890401));
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSConsumptionTaxHandler {
	private $rates;
	static private $instance;

	/**
	 * @access private
	 */
	private function __construct () {
		require(BSConfigManager::getInstance()->compile('consumption_tax'));
		$this->rates = new BSArray;
		foreach ($config['rates'] as $row) {
			$date = new BSDate($row['start_date']);
			$this->rates[$date->format('Y-m-d')] = new BSArray(array(
				'start_date' => $date,
				'rate' => (float)$row['rate'],
			));
		}
		$this->rates->sort();
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSConsumptionTaxHandler インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (!self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * @access public
	 */
	public function __clone () {
		throw new BSSingletonException('"%s"はコピー出来ません。', __CLASS__);
	}

	/**
	 * 税率を返す
	 *
	 * @access public
	 * @param BSDate $date 対象日、指定がない場合は現在
	 * @return float 税率
	 * @static
	 */
	public function getRate (BSDate $date = null) {
		if (!$date) {
			$date = BSDate::getNow();
		}

		$rate = 0;
		foreach ($this->rates as $row) {
			if ($date->isAgo($row['start_date'])) {
				break;
			}
			$rate = $row['rate'];
		}
		return $rate;
	}

	/**
	 * 税込金額を返す
	 *
	 * @access public
	 * @param float $price 税別金額
	 * @param BSDate $date 対象日、指定がない場合は現在
	 * @return integer 四捨五入された数値
	 */
	public function includeTax ($price, BSDate $date = null) {
		return BSNumeric::round($price * (1 + $this->getRate($date)));
	}

	/**
	 * 税別金額を返す
	 *
	 * @access public
	 * @param float $price 税込金額
	 * @param BSDate $date 対象日、指定がない場合は現在
	 * @return integer 四捨五入された数値
	 */
	public function excludeTax ($price, BSDate $date = null) {
		return BSNumeric::round($price / (1 + $this->getRate($date)));
	}
}

/* vim:set tabstop=4: */