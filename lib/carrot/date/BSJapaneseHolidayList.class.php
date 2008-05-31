<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage date
 */

/**
 * 日本の祝日リスト
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSJapaneseHolidayList implements BSHolidayList {
	private $date;
	private $url;
	private $holidays;

	/**
	 * コンストラクタ
	 *
	 * 対象日付の年月のみ参照され、日は捨てられる。
	 *
	 * @access public
	 * @param BSDate $date 対象日付
	 */
	public function __construct (BSDate $date = null) {
		if ($date) {
			$this->date = clone $date;
		} else {
			$this->date = BSDate::getNow();
		}
		$this->date->setHasTime(false);
		$this->date->setAttribute('day', 1);
	}

	/**
	 * 祝日を返す
	 *
	 * @access public
	 * @return BSArray 祝日配列
	 */
	public function execute () {
		if (!BSSocket::isResolvable()) {
			return new BSArray;
		}
		if (!$this->holidays) {
			$name = get_class($this) . '.' . $this->getDate()->format('Y-m');
			$expire = BSDate::getNow()->setAttribute('month', '-1');
			if ($holidays = BSController::getInstance()->getAttribute($name, $expire)) {
				$holidays = new BSArray($holidays);
			} else {
				try {
					$xml = new BSXMLDocument;
					$xml->setContents($this->getURL()->fetch());
					$holidays = $this->parse($xml);
					BSController::getInstance()->setAttribute($name, $holidays->getParameters());
				} catch (Exception $e) {
					throw new BSDateException('祝日が取得できません。');
				}
			}
			$this->holidays = $holidays;
		}
		return $this->holidays;
	}

	/**
	 * 祝日を返す
	 *
	 * executeのエイリアス
	 *
	 * @access public
	 * @return BSArray 祝日配列
	 * @final
	 */
	final public function getHolidays () {
		return $this->execute();
	}

	/**
	 * 祝日XMLをパースして配列を返す
	 *
	 * @access private
	 * @param BSXMLDocument $xml 祝日XML
	 * @return BSArray 祝日配列
	 */
	private function parse (BSXMLDocument $xml) {
		if (!$result = $xml->getElement('result')) {
			throw new BSXMLException('result要素がありません。');
		}

		$holidays = new BSArray;
		foreach ($result as $element) {
			if ($element->getName() == 'day') {
				$holidays->setParameter(
					$element->getElement('mday')->getBody(),
					$element->getElement('hname')->getBody()
				);
			}
		}
		return $holidays;
	}

	/**
	 * 対象日付を返す
	 *
	 * @access public
	 * @return BSDate 対象日付
	 */
	public function getDate () {
		return $this->date;
	}

	/**
	 * カレンダーのURLを返す
	 *
	 * @access public
	 * @return BSURL カレンダーのURL
	 */
	public function getURL () {
		if (!defined('BS_HOLIDAY_JA_URL')) {
			throw new BSDateException('定数 "BS_HOLIDAY_JA_URL" が未定義です。');
		}
		if (!$this->url) {
			$this->url = new BSURL(BS_HOLIDAY_JA_URL);
		}

		$params = array(
			'y' => $this->getDate()->getAttribute('year'),
			'm' => $this->getDate()->getAttribute('month'),
			't' => 'h',
		);
		$this->url->setParameters($params);

		return $this->url;
	}
}

/* vim:set tabstop=4 ai: */
?>