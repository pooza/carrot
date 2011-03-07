<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent
 */

/**
 * ユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @abstract
 */
abstract class BSUserAgent implements ArrayAccess, BSAssignable {
	private $type;
	protected $attributes;
	protected $bugs;
	protected $supports;
	protected $renderDigest;
	static private $denied;
	const ACCESSOR = 'ua';
	const DEFAULT_NAME = 'Mozilla/4.0';

	/**
	 * @access protected
	 * @param string $name ユーザーエージェント名
	 */
	protected function __construct ($name = null) {
		$this->attributes = new BSArray;
		$this->attributes['name'] = $name;
		$this->attributes['type'] = $this->getType();
		$this->attributes['type_lower'] = BSString::toLower($this->getType());
		$this->attributes['is_' . BSString::underscorize($this->getType())] = true;
		$this->attributes['is_mobile'] = $this->isMobile();
		$this->attributes['is_smartphone'] = $this->isSmartPhone();
		$this->attributes['is_legacy'] = $this->isLegacy();
		$this->attributes['is_denied'] = $this->isDenied();
		$this->attributes['is_attachable'] = $this->isAttachable();
		$this->attributes['is_html5_supported'] = $this->isHTML5Supported();
		$this->bugs = new BSArray;
		$this->supports = new BSArray;
	}

	/**
	 * インスタンスを生成して返す
	 *
	 * @access public
	 * @param string $useragent UserAgent名
	 * @param string $type タイプ名
	 * @return BSUserAgent インスタンス
	 * @static
	 */
	static public function getInstance ($useragent, $type = null) {
		if (!$type) {
			$type = self::getDefaultType($useragent);
		}
		$class = BSClassLoader::getInstance()->getClass($type, 'UserAgent');
		return new $class($useragent);
	}

	/**
	 * 規定タイプ名を返す
	 *
	 * @access public
	 * @param string $name UserAgent名
	 * @return string タイプ名
	 * @static
	 */
	static public function getDefaultType ($name) {
		foreach (self::getTypes() as $type) {
			$class = BSClassLoader::getInstance()->getClass($type, 'UserAgent');
			$instance = new $class;
			if (mb_ereg($instance->getPattern(), $name)) {
				return $type;
			}
		}
		return 'Default';
	}

	/**
	 * レガシー環境/旧機種か？
	 *
	 * @access public
	 * @return boolean レガシーならばTrue
	 */
	public function isLegacy () {
		return false;
	}

	/**
	 * 非対応のUserAgentか？
	 *
	 * @access public
	 * @return boolean 非対応のUserAgentならTrue
	 */
	public function isDenied () {
		if ($type = self::getDeniedTypes()->getParameter($this->getType())) {
			$values = new BSArray($type);
			if ($values['denied']) {
				return true;
			}
			if ($values['legacy_denied'] && $this->isLegacy()) {
				return true;
			}

			// $patterns変数への代入は、PHP5.1対応。
			if (BSArray::isArray($patterns = $values['denied_patterns'])) {
				foreach ($patterns as $pattern) {
					if (BSString::isContain($pattern, $this->getName())) {
						return true;
					}
				}
			}
		}
		return false;
	}

	/**
	 * HTML5対応か？
	 *
	 * @access public
	 * @return boolean HTML5対応ならTrue
	 */
	public function isHTML5Supported () {
		return false;
	}

	/**
	 * 添付可能か？
	 *
	 * @access public
	 * @return boolean 添付可能ならTrue
	 */
	public function isAttachable () {
		return true;
	}

	/**
	 * ビューを初期化
	 *
	 * @access public
	 * @param BSSmartyView 対象ビュー
	 * @return boolean 成功時にTrue
	 */
	public function initializeView (BSSmartyView $view) {
		$controller = BSController::getInstance();
		$request = BSRequest::getInstance();
		$user = BSUser::getInstance();

		$view->getRenderer()->setUserAgent($this);
		$view->getRenderer()->addModifier('sanitize');
		$view->getRenderer()->addOutputFilter('trim');
		$view->setAttributes($request->getAttributes());
		$view->setAttribute('module', $view->getModule());
		$view->setAttribute('action', $view->getAction());
		$view->setAttribute('errors', $request->getErrors());
		$view->setAttribute('params', $request->getParameters());
		$view->setAttribute('credentials', $user->getCredentials());
		$view->setAttribute('client_host', $request->getHost());
		$view->setAttribute('server_host', $controller->getHost());
		$view->setAttribute('has_proxy_server', $controller->hasServerSideCache());
		$view->setAttribute('has_server_side_cache', $controller->hasServerSideCache());
		$view->setAttribute('is_ssl', $request->isSSL());
		$view->setAttribute('is_debug', BS_DEBUG);
		$view->setAttribute('session', array(
			'name' => $request->getSession()->getName(),
			'id' => $request->getSession()->getID(),
		));
		return true;
	}

	/**
	 * セッションハンドラを生成して返す
	 *
	 * @access public
	 * @return BSSessionHandler
	 */
	public function createSession () {
		return new BSSessionHandler;
	}

	/**
	 * クエリーパラメータを返す
	 *
	 * @access public
	 * @return BSWWWFormRenderer
	 */
	public function getQuery () {
		$query = new BSWWWFormRenderer;
		if (BS_DEBUG || BSUser::getInstance()->isAdministrator()) {
			$request = BSRequest::getInstance();
			$names = new BSArray(array(
				self::ACCESSOR,
				BSTridentUserAgent::FORCE_MODE_ACCESSOR,
			));
			foreach ($names as $name) {
				if (BSString::isBlank($value = $request[$name])) {
					$query->removeParameter($name);
				} else {
					$query[$name] = $value;
				}
			}
		}
		return $query;
	}

	/**
	 * ユーザーエージェント名を返す
	 *
	 * @access public
	 * @return string ユーザーエージェント名
	 */
	public function getName () {
		return $this->attributes['name'];
	}

	/**
	 * ユーザーエージェント名を設定
	 *
	 * @access public
	 * @param string $name ユーザーエージェント名
	 */
	public function setName ($name) {
		return $this->attributes['name'];
	}

	/**
	 * サポートされているか？
	 *
	 * @access public
	 * @param string $name サポート名
	 * @return boolean サポートがあるならTrue
	 */
	public function hasSupport ($name) {
		return !!$this->supports[$name];
	}

	/**
	 * バグがあるか？
	 *
	 * @access public
	 * @param string $name バグ名
	 * @return boolean バグがあるならTrue
	 */
	public function hasBug ($name) {
		return !!$this->bugs[$name];
	}

	/**
	 * 属性を返す
	 *
	 * @access public
	 * @param string $name 属性名
	 * @return string 属性値
	 */
	public function getAttribute ($name) {
		return $this->attributes[$name];
	}

	/**
	 * 全ての基本属性を返す
	 *
	 * @access public
	 * @return BSArray 属性の配列
	 */
	public function getAttributes () {
		return $this->attributes;
	}

	/**
	 * プラットホームを返す
	 *
	 * @access public
	 * @return string プラットホーム
	 */
	public function getPlatform () {
		if (!$this->attributes['platform']) {
			$pattern = '^Mozilla/[[:digit:]]\\.[[:digit:]]+ \(([^;]+);';
			if (mb_ereg($pattern, $this->getName(), $matches)) {
				$this->attributes['platform'] = $matches[1];
			}
		}
		return $this->attributes['platform'];
	}

	/**
	 * ケータイ環境か？
	 *
	 * @access public
	 * @return boolean ケータイ環境ならTrue
	 */
	public function isMobile () {
		return false;
	}

	/**
	 * スマートフォンか？
	 *
	 * @access public
	 * @return boolean スマートフォンならTrue
	 */
	public function isSmartPhone () {
		return false;
	}

	/**
	 * タブレット型か？
	 *
	 * @access public
	 * @return boolean タブレット型ならTrue
	 */
	public function isTablet () {
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
				__CLASS__,
				(int)$this->hasSupport('html5_video_webm'),
				(int)$this->hasSupport('html5_video_h264'),
				(int)$this->hasSupport('html5_audio_aac'),
				(int)$this->hasSupport('html5_audio_mp3'),
				(int)$this->hasSupport('html5_audio_ogg'),
			)));
		}
		return $this->renderDigest;
	}

	/**
	 * アップロードボタンのラベルを返す
	 *
	 * @access public
	 * @return string アップロードボタンのラベル
	 */
	public function getUploadButtonLabel () {
		return '参照...';
	}

	/**
	 * ダウンロード用にエンコードされたファイル名を返す
	 *
	 * @access public
	 * @param string $name ファイル名
	 * @return string エンコード済みファイル名
	 */
	public function encodeFileName ($name) {
		$name = BSMIMEUtility::encode($name);
		return BSString::sanitize($name);
	}

	/**
	 * 画像マネージャを生成して返す
	 *
	 * @access public
	 * @param integer $flags フラグのビット列
	 * @return BSImageManager 画像マネージャ
	 */
	public function createImageManager ($flags = null) {
		$images = new BSImageManager($flags);
		$images->setUserAgent($this);
		return $images;
	}

	/**
	 * 画面情報を返す
	 *
	 * @access public
	 * @return BSArray 画面情報
	 */
	public function getDisplayInfo () {
		return new BSArray;
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
		$formatter = new BSStringFormat('CarrotLib.handleGPS(\'%s\')');
		$formatter[] = BSURL::encode($url->getURL()->getContents());
		$wrapper = BSURL::getInstance('javascript:' . $formatter->getContents());

		$element = new BSAnchorElement;
		$element->setURL($wrapper);
		$element->setBody($label);
		return $element;
	}

	/**
	 * 一致すべきパターンを返す
	 *
	 * @access public
	 * @return string パターン
	 * @abstract
	 */
	abstract public function getPattern ();

	/**
	 * タイプを返す
	 *
	 * @access public
	 * @return string タイプ
	 */
	public function getType () {
		if (!$this->type) {
			mb_ereg('^BS([[:alnum:]]+)UserAgent$', get_class($this), $matches);
			$this->type = $matches[1];
		}
		return $this->type;
	}

	/**
	 * 規定の画像形式を返す
	 *
	 * @access public
	 * @return string 規定の画像形式
	 */
	public function getDefaultImageType () {
		return BS_IMAGE_THUMBNAIL_TYPE;
	}

	/**
	 * @access public
	 * @param string $key 添え字
	 * @return boolean 要素が存在すればTrue
	 */
	public function offsetExists ($key) {
		return $this->attributes->hasParameter($key);
	}

	/**
	 * @access public
	 * @param string $key 添え字
	 * @return mixed 要素
	 */
	public function offsetGet ($key) {
		return $this->attributes[$key];
	}

	/**
	 * @access public
	 * @param string $key 添え字
	 * @param mixed 要素
	 */
	public function offsetSet ($key, $value) {
		throw new BSUserAgentException('属性を更新できません。');
	}

	/**
	 * @access public
	 * @param string $key 添え字
	 */
	public function offsetUnset ($key) {
		throw new BSUserAgentException('属性を削除できません。');
	}

	/**
	 * アサインすべき値を返す
	 *
	 * @access public
	 * @return mixed アサインすべき値
	 */
	public function getAssignValue () {
		return $this->attributes;
	}

	/**
	 * 全てのタイプ情報を返す
	 *
	 * @access protected
	 * @return BSArray 全てのタイプ情報
	 * @static
	 */
	static protected function getDeniedTypes () {
		if (!self::$denied) {
			self::$denied = new BSArray;
			$configure = BSConfigManager::getInstance();
			self::$denied->setParameters($configure->compile('useragent/carrot'));
			self::$denied->setParameters($configure->compile('useragent/application'));
		}
		return self::$denied;
	}

	/**
	 * 登録済みのタイプを配列で返す
	 *
	 * @access private
	 * @return BSArray タイプリスト
	 * @static
	 */
	static private function getTypes () {
		return new BSArray(array(
			'Trident',
			'Gecko',
			'iPhone',
			'Android',
			'iPad',
			'WebKit',
			'Opera',
			'Tasman',
			'LegacyMozilla',
			'Docomo',
			'Au',
			'SoftBank',
			'Console',
			'Default',
		));
	}
}

/* vim:set tabstop=4: */
