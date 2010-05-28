<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent
 */

/**
 * ユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSUserAgent implements BSAssignable {
	private $type;
	protected $attributes;
	protected $bugs;
	static private $denied;
	const ACCESSOR = 'ua';
	const DEFAULT_NAME = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)';

	/**
	 * @access public
	 * @param string $name ユーザーエージェント名
	 */
	public function __construct ($name = null) {
		$this->attributes = new BSArray;
		$this->attributes['name'] = $name;
		$this->attributes['type'] = $this->getType();
		$this->attributes['is_' . BSString::underscorize($this->getType())] = true;
		$this->attributes['is_legacy'] = $this->isLegacy();
		$this->attributes['is_denied'] = $this->isDenied();
		$this->bugs = new BSArray;
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
	 * @param string $useragent UserAgent名
	 * @return string タイプ名
	 * @static
	 */
	static public function getDefaultType ($useragent) {
		foreach (self::getTypes() as $type) {
			$instance = BSClassLoader::getInstance()->getObject($type, 'UserAgent');
			if (mb_ereg($instance->getPattern(), $useragent)) {
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
	 * ビューを初期化
	 *
	 * @access public
	 * @param BSSmartyView 対象ビュー
	 * @return boolean 成功時にTrue
	 */
	public function initializeView (BSSmartyView $view) {
		$view->getRenderer()->setUserAgent($this);
		$view->getRenderer()->addModifier('sanitize');
		$view->getRenderer()->addOutputFilter('trim');
		$view->setAttributes(BSRequest::getInstance()->getAttributes());
		$view->setAttribute('module', $view->getModule());
		$view->setAttribute('action', $view->getAction());
		$view->setAttribute('errors', BSRequest::getInstance()->getErrors());
		$view->setAttribute('params', BSRequest::getInstance()->getParameters());
		$view->setAttribute('credentials', BSUser::getInstance()->getCredentials());
		$view->setAttribute('client_host', BSRequest::getInstance()->getHost());
		$view->setAttribute('server_host', BSController::getInstance()->getHost());
		$view->setAttribute('has_proxy_server', $view->hasProxyServer());
		$view->setAttribute('is_ssl', BSRequest::getInstance()->isSSL());
		$view->setAttribute('is_debug', BS_DEBUG);
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
			if (!BSString::isBlank($name = $request[self::ACCESSOR])) {
				$query[self::ACCESSOR] = $name;
			}
			if (!!$request['preview']) {
				$query['preview'] = 1;
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
	 * バグがあるか？
	 *
	 * @access public
	 * @param string $name バグ名
	 * @return boolean バグがあるならTrue
	 */
	public function hasBug ($name) {
		return ($this->bugs[$name] || $this->bugs['general']);
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
	 * アサインすべき値を返す
	 *
	 * @access public
	 * @return mixed アサインすべき値
	 */
	public function getAssignValue () {
		return $this->attributes;
	}

	/**
	 * 既定のユーザーエージェント名を返す
	 *
	 * @access public
	 * @return BSArray ユーザーエージェント名
	 * @static
	 */
	static public function getDefaultNames () {
		return new BSArray(array(
			'pc' => self::DEFAULT_NAME,
			'mobile' => BSMobileUserAgent::DEFAULT_NAME,
		));
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
