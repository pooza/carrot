<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage request
 */

/**
 * Webリクエスト
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSWebRequest extends BSRequest {

	/**
	 * @access protected
	 */
	protected function __construct () {
		$this->setMethod($this->controller->getAttribute('REQUEST_METHOD'));
	}

	/**
	 * メソッドを設定
	 *
	 * @access public
	 * @param integer $method メソッド
	 */
	public function setMethod ($method) {
		parent::setMethod($method);
		switch ($this->getMethod()) {
			case 'GET':
			case 'HEAD':
				$this->setParameters($_GET);
				break;
			default:
				$this->setParameters($_GET);
				$this->setParameters($_POST);
				foreach ($_FILES as $key => $info) {
					if (!BSString::isBlank($info['name'])) {
						$info['is_file'] = true;
						$this[$key] = $info;
					}
				}
				break;
		}
	}

	/**
	 * 出力内容を返す
	 *
	 * @access public
	 * @return string 出力内容
	 */
	public function getContents () {
		if (!$this->contents) {
			$contents = new BSArray;
			$contents[] = $this->getRequestLine();
			foreach ($this->getHeaders() as $header) {
				$contents[] = $header->getName() . ': ' . $header->getContents();
			}
			$contents[] = null;
			$contents[] = $this->getBody();
			$this->contents = $contents->join(self::LINE_SEPARATOR);
		}
		return $this->contents;
	}

	/**
	 * httpバージョンを返す
	 *
	 * @access public
	 * @return string httpバージョン
	 */
	public function getVersion () {
		if (!$this->version) {
			$version = $this->controller->getAttribute('SERVER_PROTOCOL');
			$this->version = BSString::explode('/', $version)[1];
		}
		return $this->version;
	}

	/**
	 * レンダラーを返す
	 *
	 * @access public
	 * @return BSRenderer レンダラー
	 */
	public function getRenderer () {
		if (!extension_loaded('http')) {
			throw new BSHTTPException('httpモジュールがロードされていません。');
		}
		if (!$this->renderer) {
			$this->renderer = new BSRawRenderer;
			$this->renderer->setContents(http_get_request_body());
		}
		return $this->renderer;
	}

	/**
	 * ヘッダ一式を返す
	 *
	 * @access public
	 * @return string[] ヘッダ一式
	 */
	public function getHeaders () {
		if (!$this->headers) {
			$this->headers = new BSArray;
			if (extension_loaded('http')) {
				$headers = http_get_request_headers();
			} else if (BSString::isContain('apache', PHP_SAPI)) {
				$headers = apache_request_headers();
			} else {
				$headers = [];
				foreach ($_SERVER as $key => $value) {
					if (mb_ereg('HTTP_(.*)', $key, $matches)) {
						$headers[str_replace('_', '-', $matches[1])] = $value;
					}
				}
			}
			foreach ($headers as $key => $value) {
				$this->setHeader($key, $value);
			}
		}
		return $this->headers;
	}

	/**
	 * 送信先URLを返す
	 *
	 * @access public
	 * @return BSURL 送信先URL
	 */
	public function getURL () {
		if (!$this->url) {
			$url = 'http';
			if ($this->isSSL()) {
				$url .= 's';
			}
			$url .= "://" . $this->controller->getHost()->getName();
			$this->url = BSURL::create($url);
			$this->url['path'] = $this->controller->getAttribute('REQUEST_URI');
		}
		return $this->url;
	}

	/**
	 * Cookie対応環境か？
	 *
	 * 環境自体がCookieに対応するかではなく、carrot上でCookie対応とみなすかどうかを返す。
	 *
	 * @access public
	 * @return boolean Cookie対応環境ならTrue
	 */
	public function isEnableCookie () {
		return (!$this->isAjax()
			&& !$this->isFlash()
			&& !$this->isCarrot()
			&& !$this->isMobile()
		);
	}

	/**
	 * ケータイ環境か？
	 *
	 * @access public
	 * @return boolean ケータイ環境ならTrue
	 */
	public function isMobile () {
		return $this->getUserAgent()->isMobile();
	}

	/**
	 * スマートフォン環境か？
	 *
	 * @access public
	 * @return boolean スマートフォン環境ならTrue
	 */
	public function isSmartPhone () {
		return $this->getUserAgent()->isSmartPhone();
	}

	/**
	 * SSL環境か？
	 *
	 * @access public
	 * @return boolean SSL環境ならTrue
	 */
	public function isSSL () {
		return !!$this->controller->getAttribute('HTTPS');
	}

	/**
	 * Ajax環境か？
	 *
	 * @access public
	 * @return boolean Ajax環境ならTrue
	 */
	public function isAjax () {
		return $this->getHeader('x-requested-with') || $this->getHeader('x-prototype-version');
	}

	/**
	 * Flash環境か？
	 *
	 * @access public
	 * @return boolean Flash環境ならTrue
	 */
	public function isFlash () {
		return $this->getHeader('x-flash-version') || $this->getHeader('x-is-flash');
	}

	/**
	 * Carrot環境か？
	 *
	 * @access public
	 * @return boolean Flash環境ならTrue
	 */
	public function isCarrot () {
		return BSString::isContain(BS_CARROT_NAME, $this->getUserAgent()->getName());
	}

	/**
	 * Submitされたか？
	 *
	 * @access public
	 * @return boolean SubmitされたならTrue
	 */
	public function isSubmitted () {
		return !BSString::isBlank($this[BSFormElement::SUBMITTED_FIELD]);
	}
}

