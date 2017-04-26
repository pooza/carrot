<?php
/**
 * @package jp.co.b-shock.carrot
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
	const DEFAULT_NAME = 'DoCoMo/2.0 (c500;)';

	/**
	 * @access protected
	 * @param string $name ユーザーエージェント名
	 */
	protected function __construct ($name = null) {
		parent::__construct($name);
		$this['display'] = $this->getDisplayInfo();
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
		if (!!$this->hasSupport('cookie')) {
			return new BSSessionHandler;
		} else {
			return new BSMobileSessionHandler;
		}
	}

	/**
	 * クエリーパラメータを返す
	 *
	 * @access public
	 * @return BSWWWFormRenderer
	 */
	public function getQuery () {
		$query = parent::getQuery();
		if (!$this->hasSupport('cookie')) {
			$query[$this->request->getSession()->getName()] = $session->getID();
		}
		if ($this->controller->hasServerSideCache()) {
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
	 * キャリア名を返す
	 *
	 * @access public
	 * @return string キャリア名
	 */
	public function getCarrier () {
		if (mb_ereg('^BS([A-Za-z]+)UserAgent$', get_class($this), $matches)) {
			return $matches[1];
		}
	}

	/**
	 * 規定の画像形式を返す
	 *
	 * @access public
	 * @return string 規定の画像形式
	 */
	public function getDefaultImageType () {
		return 'image/png';
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
	 * 画面情報を返す
	 *
	 * @access public
	 * @return BSArray 画面情報
	 */
	public function getDisplayInfo () {
		return new BSArray([
			'width' => BS_IMAGE_MOBILE_SIZE_QVGA_WIDTH,
			'height' => BS_IMAGE_MOBILE_SIZE_QVGA_HEIGHT,
		]);
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
	 * ダイジェストを返す
	 *
	 * @access public
	 * @return string ダイジェスト
	 */
	public function digest () {
		if (!$this->digest) {
			$this->digest = BSCrypt::digest([
				get_class($this),
				$this->getDisplayInfo()['width'],
				(int)$this->hasSupport('cookie'),
			]);
		}
		return $this->digest;
	}
}

/* vim:set tabstop=4: */
