<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent.mobile
 */

/**
 * モバイルユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @abstract
 */
abstract class BSMobileUserAgent extends BSUserAgent implements BSUserIdentifier {
	private $carrier;
	const DEFAULT_NAME = 'DoCoMo/2.0';

	/**
	 * @access protected
	 * @param string $name ユーザーエージェント名
	 */
	protected function __construct ($name = null) {
		parent::__construct($name);
		$this->attributes['id'] = $this->getID();
		$this->attributes['display'] = $this->getDisplayInfo();
		$this->attributes['gps'] = $this->getCarrier()->getGPSInfo();
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
		$query = parent::getQuery();
		$session = BSRequest::getInstance()->getSession();
		$query[$session->getName()] = $session->getID();
		if (BSController::getInstance()->hasServerSideCache()) {
			$query['guid'] = 'ON';
		}
		return $query;
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
	 * デコメールの形式を返す
	 *
	 * @access public
	 * @return string デコメールの形式
	 */
	public function getDecorationMailType () {
		return $this->getModule()->getDecorationMailType();
	}

	/**
	 * 画面情報を返す
	 *
	 * @access public
	 * @return BSArray 画面情報
	 */
	public function getDisplayInfo () {
		return new BSArray(array(
			'width' => BS_IMAGE_MOBILE_SIZE_WIDTH,
			'height' => BS_IMAGE_MOBILE_SIZE_HEIGHT,
		));
	}

	/**
	 * ムービー表示用のXHTML要素を返す
	 *
	 * @access public
	 * @param BSParameterHolder $params パラメータ配列
	 * @param BSUserAgent $useragent 対象ブラウザ
	 * @return BSDivisionElement 要素
	 */
	public function getMovieElement (BSParameterHolder $params) {
		$container = new BSDivisionElement;
		$container->addElement($anchor = new BSAnchorElement);
		$anchor->setURL($params['url']);
		$anchor->setBody($params['label']);
		return $container;
	}

	/**
	 * Flash表示用のXHTML要素を返す
	 *
	 * @access public
	 * @param BSParameterHolder $params パラメータ配列
	 * @param BSUserAgent $useragent 対象ブラウザ
	 * @return BSDivisionElement 要素
	 */
	public function getFlashElement (BSParameterHolder $params) {
		$container = new BSDivisionElement;
		$object = $container->addElement(new BSFlashLightObjectElement);
		$object->setURL($params['url']);
		$object->setAttribute('width', $params['width']);
		$object->setAttribute('height', $params['height']);
		return $container;
	}

	/**
	 * GPS情報を取得するリンクを返す
	 *
	 * @access public
	 * @param BSHTTPRedirector $url 対象リンク
	 * @param string $label ラベル
	 * @return BSAnchorElement リンク
	 */
	public function getGPSAnchorElement (BSHTTPRedirector $url, $label) {
		return $this->getCarrier()->getGPSAnchorElement($url, $label);
	}

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
	 * レンダーダイジェストを返す
	 *
	 * @access public
	 * @return string レンダーダイジェスト
	 */
	public function getRenderDigest () {
		if (!$this->renderDigest) {
			$this->renderDigest = BSCrypt::getDigest(new BSArray(array(
				get_class($this),
				$this->getDisplayInfo()->getParameter('width'),
			)));
		}
		return $this->renderDigest;
	}

	/**
	 * 端末IDを返す
	 *
	 * @access public
	 * @return string 端末ID
	 */
	public function getID () {
		if (BS_DEBUG) {
			return BSCrypt::getDigest(BSRequest::getInstance()->getHost()->getName());
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
	 * @param string $password パスワード
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
