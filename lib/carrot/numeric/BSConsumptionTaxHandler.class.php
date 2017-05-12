<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage numeric
 */

/**
 * 消費税計算機
 *
 * //当時100円のドクターペッパーが、1989/4/1より103円になったことを確認。
 * $tax = BSConsumptionTaxHandler::getInstance();
 * $price = $tax->includeTax(100, BSDate::create(19890401));
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSConsumptionTaxHandler {
	use BSSingleton;
	private $rates;

	/**
	 * @access protected
	 */
	protected function __construct () {
		$config = BSConfigManager::getInstance()->compile('consumption_tax');
		$this->rates = new BSArray;
		foreach ($config['rates'] as $row) {
			$date = BSDate::create($row['start_date']);
			$this->rates[$date->format('Y-m-d')] = new BSArray([
				'start_date' => $date,
				'rate' => (float)$row['rate'],
			]);
		}
		$this->rates->sort();
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
			if ($date->isPast($row['start_date'])) {
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

