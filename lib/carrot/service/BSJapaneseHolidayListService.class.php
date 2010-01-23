<?php
/**
 * @package org.carrot-framework
 * @subpackage service
 */

/**
 * Finds.jp 「曜日・祝日計算サービス」クライアント
 *
 * 同サービスの祝日機能のみを実装。
 * 曜日を知りたい場合は、BSDate::getWeekday等を利用すること。
 *
 * サンプルコード
 * $holidays = new BSJapaneseHolidayList;
 * $holidays->setDate(BSDate::getNow());
 * p($holidays[5]); //当月5日の祝日の名前
 * p($holidays->getHolidays()); //当月のすべての祝日を配列で
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @link http://www.finds.jp/wsdocs/calendar/
 */
class BSJapaneseHolidayListService extends BSCurlHTTP implements BSHolidayList {
	private $date;
	private $holidays;
	const DEFAULT_HOST = 'www.finds.jp';
	const PATH = '/ws/calendar.php';

	/**
	 * @access public
	 * @param BSHost $host ホスト
	 * @param integer $port ポート
	 */
	public function __construct (BSHost $host = null, $port = null) {
		if (!$host) {
			$host = new BSHost(self::DEFAULT_HOST);
		}
		parent::__construct($host, $port);
	}

	/**
	 * 対象日付を返す
	 *
	 * @access public
	 * @return BSDate 対象日付
	 */
	public function getDate () {
		if (!$this->date) {
			throw new BSInitializationException(
				get_class($this) . 'の対象日付が設定されていません。'
			);
		}
		return $this->date;
	}

	/**
	 * 対象日付を設定
	 *
	 * 対象日付の年月のみ参照され、日は捨てられる。
	 *
	 * @access public
	 * @param BSDate $date 対象日付
	 */
	public function setDate (BSDate $date = null) {
		if ($date) {
			$this->date = clone $date;
		} else {
			$this->date = BSDate::getNow();
		}
		$this->date->setHasTime(false);
		$this->date['day'] = 1;
	}

	/**
	 * 祝日を返す
	 *
	 * @access public
	 * @return BSArray 祝日配列
	 */
	public function getHolidays () {
		if (!$this->holidays) {
			$name = sprintf('%s.%s', get_class($this), $this->getDate()->format('Y-m'));
			$date = BSDate::getNow()->setAttribute('month', '-1');
			$holidays = BSController::getInstance()->getAttribute($name, $date);
			if (BSString::isBlank($holidays)) {
				$holidays = $this->query();
				BSController::getInstance()->setAttribute($name, $holidays);
			}
			$this->holidays = new BSArray($holidays);
		}
		return $this->holidays;
	}

	/**
	 * クエリーを実行
	 *
	 * @access private
	 * @return BSArray 祝日配列
	 */
	private function query () {
		try {
			$url = BSURL::getInstance();
			$url['host'] = $this->getHost();
			$url['path'] = self::PATH;
			$url->setParameter('y', $this->getDate()->getAttribute('year'));
			$url->setParameter('m', $this->getDate()->getAttribute('month'));
			$url->setParameter('t', 'h');
			$response = $this->sendGetRequest($url->getFullPath());
			$xml = new BSXMLDocument;
			$xml->setContents($response->getRenderer()->getContents());
			return $this->parse($xml);
		} catch (Exception $e) {
			throw new BSHTTPException('祝日が取得できません。');
		}
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
	 * 要素が存在するか？
	 *
	 * @access public
	 * @param string $key 添え字
	 * @return boolean 要素が存在すればTrue
	 */
	public function offsetExists ($key) {
		return $this->getHolidays()->hasParameter($key);
	}

	/**
	 * 要素を返す
	 *
	 * @access public
	 * @param string $key 添え字
	 * @return mixed 要素
	 */
	public function offsetGet ($key) {
		return $this->getHolidays()->getParameter($key);
	}

	/**
	 * 要素を設定
	 *
	 * @access public
	 * @param string $key 添え字
	 * @param mixed 要素
	 */
	public function offsetSet ($key, $value) {
		throw new BSInitializationException(get_class($this) . 'は更新できません。');
	}

	/**
	 * 要素を削除
	 *
	 * @access public
	 * @param string $key 添え字
	 */
	public function offsetUnset ($key) {
		throw new BSInitializationException(get_class($this) . 'は更新できません。');
	}
}

/* vim:set tabstop=4: */
