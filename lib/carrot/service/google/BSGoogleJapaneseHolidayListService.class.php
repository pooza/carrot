<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage service.google
 */

/**
 * Google Calendar 祝日取得
 *
 * サンプルコード
 * $holidays = new BSGoogleJapaneseHolidayListService;
 * $holidays->setDate(BSDate::getNow());
 * p($holidays[5]); //当月5日の祝日の名前
 * p($holidays->getHolidays()); //当月のすべての祝日を配列で
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @link http://www.finds.jp/wsdocs/calendar/
 */
class BSGoogleJapaneseHolidayListService extends BSCurlHTTP implements BSHolidayList, BSSerializable {
	use BSSerializableMethods;
	protected $digest;
	private $date;
	private $holidays;
	const DEFAULT_HOST = 'www.googleapis.com';
	const HOLIDAY_ID = 'japanese__ja@holiday.calendar.google.com';

	/**
	 * @access public
	 * @param BSHost $host ホスト
	 * @param integer $port ポート
	 */
	public function __construct (BSHost $host = null, $port = null) {
		if (!$host) {
			$host = new BSHost(self::DEFAULT_HOST);
			$port = BSNetworkService::getPort('https');
		}
		parent::__construct($host, $port);
		$this->holidays = BSArray::create();
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
			$this->date = BSDate::getNow()->clearTime();
		}
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
	 * @access public
	 * @param string $href パス
	 * @return BSHTTPURL リクエストURL
	 */
	public function createRequestURL ($href) {
		$url = parent::createRequestURL($href);
		$url->setParameter('key', BS_SERVICE_GOOGLE_CALENDAR_API_KEY);
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
		return $this->getHolidays()[$key];
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
	 * ダイジェストを返す
	 *
	 * @access public
	 * @return string ダイジェスト
	 */
	public function digest () {
		if (!$this->digest) {
			$date = $this->getDate();
			$this->digest = BSCrypt::digest([
				get_class($this),
				$date['year'],
				$date['month'],
			]);
		}
		return $this->digest;
	}

	/**
	 * シリアライズ
	 *
	 * @access public
	 */
	public function serialize () {
		try {
			$date = $this->getDate();
			$url = $this->createRequestURL(
				'/calendar/v3/calendars/' . self::HOLIDAY_ID . '/events'
			);
			$url->setParameter('timeMin', $date->format('c'));
			$url->setParameter('timeMax', $date->getLastDateOfMonth()->format('c'));
			$url->setParameter('maxResults', 30);
			$url->setParameter('orderBy', 'startTime');
			$url->setParameter('singleEvents', 'true');
			$response = $this->sendGET($url->getFullPath());

			$json = new BSJSONRenderer;
			$json->setContents($response->getRenderer()->getContents());
			$result = $json->getResult();

			$holidays = BSArray::create();
			if (isset($result['items']) && is_array($result['items'])) {
				foreach ($result['items'] as $entry) {
					$date = BSDate::create($entry['start']['date']);
					$holidays[$date['day']] = $entry['summary'];
				}
			}
			$this->controller->setAttribute($this, $holidays);
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
		$date = BSDate::getNow()->setParameter('month', '-1');
		return $this->controller->getAttribute($this, $date);
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('Google Calendar 祝日取得 "%s"', $this->getName());
	}
}

