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
abstract class BSMobileUserAgent extends BSUserAgent {
	private $carrier;
	const DEFAULT_NAME = 'DoCoMo/2.0';

	/**
	 * @access protected
	 * @param string $name ユーザーエージェント名
	 */
	protected function __construct ($name = null) {
		parent::__construct($name);
		$this['id'] = $this->getID();
		$this['display'] = $this->getDisplayInfo();
		$this['gps'] = $this->getCarrier()->getGPSInfo();
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
		$constants = new BSConstantHandler('IMAGE_MOBILE_TYPE');
		return $constants[$this->getCarrier()->getName()];
	}

	/**
	 * 規定のエンコードを返す
	 *
	 * @access public
	 * @return string 規定のエンコード
	 */
	public function getDefaultEncoding () {
		return 'sjis-win';
	}

	/**
	 * デコメールの形式を返す
	 *
	 * @access public
	 * @return string デコメールの形式
	 */
	public function getDecorationMailType () {
		return $this->getCarrier()->getDecorationMailType();
	}

	/**
	 * 画面情報を返す
	 *
	 * @access public
	 * @return BSArray 画面情報
	 */
	public function getDisplayInfo () {
		return new BSArray(array(
			'width' => BS_IMAGE_MOBILE_SIZE_QVGA_WIDTH,
			'height' => BS_IMAGE_MOBILE_SIZE_QVGA_HEIGHT,
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
	public function createMovieElement (BSParameterHolder $params) {
		$container = new BSDivisionElement;
		$anchor = $container->addElement(new BSAnchorElement);
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
	public function createFlashElement (BSParameterHolder $params) {
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
	public function createGPSAnchorElement (BSHTTPRedirector $url, $label) {
		return $this->getCarrier()->createGPSAnchorElement($url, $label);
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
	 * ダイジェストを返す
	 *
	 * @access public
	 * @return string ダイジェスト
	 */
	public function digest () {
		if (!$this->digest) {
			$this->digest = BSCrypt::digest(array(
				get_class($this),
				$this->getDisplayInfo()->getParameter('width'),
			));
		}
		return $this->digest;
	}

	/**
	 * 端末IDを返す
	 *
	 * @access public
	 * @return string 端末ID
	 */
	public function getID () {
		if (BS_DEBUG) {
			return BSCrypt::digest(BSRequest::getInstance()->getHost()->getName());
		}
	}
}

/* vim:set tabstop=4: */
