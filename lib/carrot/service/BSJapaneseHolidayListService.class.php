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
 * $holidays = new BSJapaneseHolidayListService;
 * $holidays->setDate(BSDate::getNow());
 * p($holidays[5]); //当月5日の祝日の名前
 * p($holidays->getHolidays()); //当月のすべての祝日を配列で
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @link http://www.finds.jp/wsdocs/calendar/
 */
class BSJapaneseHolidayListService extends BSCurlHTTP implements BSHolidayList, BSSerializable {
	private $date;
	private $holidays;
	const DEFAULT_HOST = 'www.finds.jp';

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
		$this->holidays = new BSArray;
	}

	/**
	 * 対象日付を返す
	 *
	 * @access public
	 * @return BSDate 対象日付
	 */
	public function getDate () {
		if (!$this->date) {
			$this->setDate();
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

		if (BSString::isBlank($this->getSerialized())) {
			$this->serialize();
		}
		$this->holidays->clear();
		$this->holidays->setParameters($this->getSerialized());
	}

	/**
	 * 祝日を返す
	 *
	 * @access public
	 * @return BSArray 祝日配列
	 */
	public function getHolidays () {
		return $this->holidays;
	}

	/**
	 * パスからリクエストURLを生成して返す
	 *
	 * @access protected
	 * @param string $href パス
	 * @return BSHTTPURL リクエストURL
	 */
	protected function createRequestURL ($href) {
		$url = parent::createRequestURL($href);
		$url->setParameter('json', 1);
		return $url;
	}

	/**
	 * @access public
	 * @param string $key 添え字
	 * @return boolean 要素が存在すればTrue
	 */
	public function offsetExists ($key) {
		return $this->getHolidays()->hasParameter($key);
	}

	/**
	 * @access public
	 * @param string $key 添え字
	 * @return mixed 要素
	 */
	public function offsetGet ($key) {
		return $this->getHolidays()->getParameter($key);
	}

	/**
	 * @access public
	 * @param string $key 添え字
	 * @param mixed 要素
	 */
	public function offsetSet ($key, $value) {
		throw new BadFunctionCallException(get_class($this) . 'は更新できません。');
	}

	/**
	 * @access public
	 * @param string $key 添え字
	 */
	public function offsetUnset ($key) {
		throw new BadFunctionCallException(get_class($this) . 'は更新できません。');
	}

	/**
	 * 属性名へシリアライズ
	 *
	 * @access public
	 * @return string 属性名
	 */
	public function serializeName () {
		return sprintf('%s.%s', get_class($this), $this->getDate()->format('Y-m'));
	}

	/**
	 * シリアライズ
	 *
	 * @access public
	 */
	public function serialize () {
		try {
			$url = $this->createRequestURL('/ws/calendar.php');
			$url->setParameter('y', $this->getDate()->getAttribute('year'));
			$url->setParameter('m', $this->getDate()->getAttribute('month'));
			$url->setParameter('t', 'h');
			$response = $this->sendGET($url->getFullPath());

			$json = new BSJSONRenderer;
			$json->setContents($response->getRenderer()->getContents());
			$result = $json->getResult();

			$holidays = new BSArray;
			if (isset($result['result']['day']) && is_array($result['result']['day'])) {
				foreach ($result['result']['day'] as $entry) {
					$holidays[$entry['mday']] = $entry['hname'];
				}
			}
			BSController::getInstance()->setAttribute($this, $holidays);
		} catch (Exception $e) {
		}
	}

	/**
	 * シリアライズ時の値を返す
	 *
	 * @access public
	 * @return mixed シリアライズ時の値
	 */
	public function getSerialized () {
		$date = BSDate::getNow()->setAttribute('month', '-1');
		return BSController::getInstance()->getAttribute($this, $date);
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return '日本の祝日(' . $this->getDate()->format('Y-m') . ')';
	}
}

/* vim:set tabstop=4: */
