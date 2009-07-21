<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent.mobile
 */

/**
 * モバイルユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSMobileUserAgent extends BSUserAgent implements BSUserIdentifier {
	private $carrier;
	private $smarty;
	const IMAGE_FULL_SCREEN = 1;
	const DEFAULT_DISPLAY_WIDTH = 240;
	const DEFAULT_DISPLAY_HEIGHT = 320;

	/**
	 * @access public
	 * @param string $name ユーザーエージェント名
	 */
	public function __construct ($name = null) {
		parent::__construct($name);
		$this->attributes['is_mobile'] = $this->isMobile();
		$this->attributes['id'] = $this->getID();
		$this->attributes['is_legacy'] = $this->isLegacy();
		$this->attributes['display'] = $this->getDisplayInfo();
		$this->attributes['query'] = new BSArray;
	}

	/**
	 * Smartyを初期化する
	 *
	 * @access public
	 * @param BSSmarty
	 */
	public function initializeSmarty (BSSmarty $smarty) {
		$smarty->setAttribute('useragent', $this->getAttributes());
		$smarty->setEncoding('sjis-win');
		$smarty->addModifier('pictogram');
		$smarty->addOutputFilter('mobile');
		$smarty->addOutputFilter('encoding');
		$smarty->addOutputFilter('trim');
		$this->smarty = $smarty;
	}

	/**
	 * セッションハンドラを設定
	 *
	 * @access public
	 * @param BSSessionHandler
	 */
	public function setSession (BSSessionHandler $session) {
		parent::setSession($session);

		$params = new BSWWWFormRenderer;
		$params->setParameters($this->attributes['query']);
		$params[$session->getName()] = $session->getID();
		if (BS_DEBUG) {
			$params[BSRequest::USER_AGENT_ACCESSOR] = $this->getName();
		}

		$this->attributes['query'] = $params->getParameters();
		$this->attributes['query_params'] = $params->getContents();
		if ($this->smarty) {
			$this->smarty->setAttribute('useragent', $this->getAttributes());
		}
	}

	/**
	 * ケータイ環境か？
	 *
	 * @access public
	 * @return boolean ケータイ環境ならTrue
	 */
	public function isMobile () {
		return true;
	}

	/**
	 * プラットホームを返す
	 *
	 * @access public
	 * @return string プラットホーム
	 */
	public function getPlatform () {
		if (!$this->attributes['platform']) {
			$this->attributes['platform'] = $this->getType();
		}
		return $this->attributes['platform'];
	}

	/**
	 * キャリアを返す
	 *
	 * @access public
	 * @return BSMobileCarrier キャリア
	 */
	public function getCarrier () {
		if (!$this->carrier) {
			$this->carrier = BSClassLoader::getInstance()->getObject(
				$this->getType(),
				'MobileCarrier'
			);
		}
		return $this->carrier;
	}

	/**
	 * 旧機種か？
	 *
	 * @access public
	 * @return boolean 旧機種ならばTrue
	 * @abstract
	 */
	abstract public function isLegacy ();

	/**
	 * 規定の画像形式を返す
	 *
	 * @access public
	 * @return string 規定の画像形式
	 */
	public function getDefaultImageType () {
		$constants = BSConstantHandler::getInstance();
		return $constants['IMAGE_MOBILE_TYPE_' . $this->getCarrier()->getName()];
	}

	/**
	 * 画像を変換
	 *
	 * @access public
	 * @param BSImage $image 対象画像
	 * @param integer $flags フラグのビット列
	 * @return BSImage 変換後の画像
	 */
	public function convertImage (BSImage $image, $flags = self::IMAGE_FULL_SCREEN) {
		$dest = clone $image;
		$dest->setType($this->getDefaultImageType());
		if ($flags & self::IMAGE_FULL_SCREEN) {
			$dest->resize($this->attributes['display']['width'], null);
		}
		return $dest;
	}

	/**
	 * 画面情報を返す
	 *
	 * @access public
	 * @return BSArray 画面情報
	 * @abstract
	 */
	abstract public function getDisplayInfo ();

	/**
	 * 端末IDを返す
	 *
	 * @access public
	 * @return string 端末ID
	 */
	public function getID () {
		if (BS_DEBUG) {
			return BSCrypt::getSHA1(
				BSRequest::getInstance()->getHost()->getName() . BS_CRYPT_SALT
			);
		}
	}

	/**
	 * ユーザーIDを返す
	 *
	 * @access public
	 * @return string ユーザーID
	 */
	public function getUserID () {
		return $this->getID();
	}

	/**
	 * 端末認証
	 *
	 * パスワードを用いず、端末個体認証を行う。
	 *
	 * @access public
	 * @params string $password パスワード
	 * @return boolean 正しいユーザーならTrue
	 */
	public function auth ($password = null) {
		return $this->getUserID() && ($this === BSRequest::getInstance()->getUserAgent());
	}
}

/* vim:set tabstop=4: */
