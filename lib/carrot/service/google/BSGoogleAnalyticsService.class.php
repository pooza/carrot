<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage service.google
 */

/**
 * Google Analytics
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSGoogleAnalyticsService extends BSParameterHolder implements BSAssignable {
	use BSSingleton, BSBasicObject;

	/**
	 * @access protected
	 */
	protected function __construct () {
		$this['id'] = BS_SERVICE_GOOGLE_ANALYTICS_ID;
		$this['domain'] = $this->getRootDomainName();
	}

	private function getRootDomainName () {
		$domain = BSString::explode('.', $this->controller->getHost()->getName());
		if ($domain->shift() == 'test') {
			$domain->shift();
		}
		$domain->unshift(null);
		return $domain->join('.');
	}

	/**
	 * アカウントIDを返す
	 *
	 * @access public
	 * @return string アカウントID
	 */
	public function getID () {
		return $this['id'];
	}

	/**
	 * アカウントIDを設定
	 *
	 * @access public
	 * @param string $id アカウントID
	 */
	public function setID ($id) {
		$this['id'] = $id;
	}

	/**
	 * トラッキングコードを返す
	 *
	 * @access public
	 * @param BSUserAgent $useragent 対象UserAgent
	 * @return string トラッキングコード
	 */
	public function getTrackingCode (BSUserAgent $useragent = null) {
		if (BS_DEBUG) {
			return null;
		}

		if (!$useragent) {
			$useragent = $this->request->getUserAgent();
		}

		if ($useragent->isMobile()) {
			$renderer = new BSImageElement;
			$renderer->setURL($this->getBeaconURL());
		} else {
			$renderer = new BSSmarty;
			$renderer->setUserAgent($useragent);
			$renderer->setTemplate('GoogleAnalytics');
			$renderer->setAttribute('params', $this);
		}
		return $renderer->getContents();
	}

	private function getBeaconURL () {
		$url = BSURL::create();
		$url['path'] = BS_SERVICE_GOOGLE_ANALYTICS_BEACON_HREF;
		$url->setParameter('guid', 'ON');
		$url->setParameter('utmac', 'MO-' . $this->getID());
		$url->setParameter('utmn', BSNumeric::getRandom(0, 0x7fffffff));
		$url->setParameter('utmp', $this->request->getURL()->getFullPath());
		if (BSString::isBlank($referer = $this->controller->getAttribute('REFERER'))) {
			$referer = '-';
		}
		$url->setParameter('utmr', $referer);
		return $url;
	}

	/**
	 * アサインすべき値を返す
	 *
	 * @access public
	 * @return mixed アサインすべき値
	 */
	public function assign () {
		return $this->params;
	}
}

