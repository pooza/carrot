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
	private $query;
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
		$this->attributes['is_attachable'] = $this->isAttachable();
		$this->attributes['display'] = $this->getDisplayInfo();
		$this->attributes['query'] = new BSArray;
	}

	/**
	 * ビューを初期化
	 *
	 * @access public
	 * @param BSSmartyView 対象ビュー
	 * @return boolean 成功時にTrue
	 */
	public function initializeView (BSSmartyView $view) {
		parent::initializeView($view);
		$view->getRenderer()->setEncoding('sjis-win');
		$view->getRenderer()->addModifier('pictogram');
		$view->getRenderer()->addOutputFilter('mobile');
		$view->getRenderer()->addOutputFilter('encoding');
		return true;
	}

	/**
	 * セッションハンドラを生成して返す
	 *
	 * @access public
	 * @return BSSessionHandler
	 */
	public function createSession () {
		return new BSMobileSessionHandler;
	}

	/**
	 * クエリーパラメータを返す
	 *
	 * @access public
	 * @return BSWWWFormRenderer
	 */
	public function getQuery () {
		if (!$this->query) {
			$this->query = new BSWWWFormRenderer;
			$this->query->setParameters($this->attributes['query']);

			$session = BSRequest::getInstance()->getSession();
			$this->query[$session->getName()] = $session->getID();
			if (BS_DEBUG) {
				$this->query[BSRequest::USER_AGENT_ACCESSOR] = $this->getName();
				$this->query['mobile_agent_id'] = $this->getID();
			}
		}
		return $this->query;
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
	 * 画面情報を返す
	 *
	 * @access public
	 * @return BSArray 画面情報
	 * @abstract
	 */
	abstract public function getDisplayInfo ();

	/**
	 * 添付可能か？
	 *
	 * @access public
	 * @return boolean 添付可能ならTrue
	 */
	public function isAttachable () {
		return false;
	}

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

	/**
	 * 認証時に与えられるクレデンシャルを返す
	 *
	 * @access public
	 * @return BSArray クレデンシャルの配列
	 */
	public function getCredentials () {
		return new BSArray;
	}
}

/* vim:set tabstop=4: */
