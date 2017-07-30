<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net.http.useragent
 */

/**
 * ユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @abstract
 */
abstract class BSUserAgent extends BSParameterHolder {
	use BSBasicObject;
	protected $bugs;
	protected $supports;
	protected $type;
	protected $digest;
	const ACCESSOR = 'ua';
	const DEFAULT_NAME = 'Mozilla/4.0';

	/**
	 * @access protected
	 * @param string $name ユーザーエージェント名
	 */
	protected function __construct ($name = null) {
		$this->bugs = BSArray::create();
		$this->supports = BSArray::create();
		$this['name'] = $name;
		$this['type'] = $this->getType();
		$this['type_lower'] = BSString::toLower($this->getType());
		$this['is_mobile'] = $this->isMobile();
		$this['is_smartphone'] = $this->isSmartPhone();
		$this['is_tablet'] = $this->isTablet();
		$this['is_legacy'] = $this->isLegacy();
		$this['display'] = $this->getDisplayInfo();

		$classes = [get_class($this)] + BSLoader::getParentClasses(get_class($this));
		foreach ($classes as $class) {
			if (mb_ereg('^BS([[:alnum:]]+)UserAgent$', $class, $matches)) {
				$this['is_' . BSString::underscorize($matches[1])] = true;
			}
		}
	}

	/**
	 * インスタンスを生成して返す
	 *
	 * @access public
	 * @param string $name UserAgent名
	 * @param string $type タイプ名
	 * @return BSUserAgent インスタンス
	 * @static
	 */
	static public function create ($name, $type = null) {
		if (!$type) {
			$type = self::getDefaultType($name);
		}
		$class = BSLoader::getInstance()->getClass($type, 'UserAgent');
		return new $class($name);
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
			$class = BSLoader::getInstance()->getClass($type, 'UserAgent');
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
	 * レガシー環境/旧機種か？
	 *
	 * isLecagyのエイリアス
	 *
	 * @access public
	 * @return boolean レガシーならばTrue
	 * @final
	 */
	final public function isDenied () {
		return $this->isLegacy();
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
		$view->setAttributes($this->request->getAttributes());
		$view->setAttribute('module', $view->getModule());
		$view->setAttribute('action', $view->getAction());
		$view->setAttribute('errors', $this->request->getErrors());
		$view->setAttribute('params', $this->request->getParameters());
		$view->setAttribute('real_useragent', $this->request->getRealUserAgent());
		$view->setAttribute('credentials', $this->user->getCredentials());
		$view->setAttribute('client_host', $this->request->getHost());
		$view->setAttribute('server_host', $this->controller->getHost());
		$view->setAttribute('has_proxy_server', $this->controller->hasServerSideCache());
		$view->setAttribute('has_server_side_cache', $this->controller->hasServerSideCache());
		$view->setAttribute('is_ssl', $this->request->isSSL());
		$view->setAttribute('is_debug', BS_DEBUG);
		$view->setAttribute('is_image_storable', BS_IMAGE_STORABLE);
		$view->setAttribute('session', [
			'name' => $this->request->getSession()->getName(),
			'id' => $this->request->getSession()->getID(),
		]);
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
		if (BS_DEBUG || $this->user->isAdministrator()) {
			$query[self::ACCESSOR] = $this->request[self::ACCESSOR];
		}
		if ($this->hasBug('cache_control')) {
			$query['at'] = BSNumeric::getRandom(1000, 9999);
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
		return $this['name'];
	}

	/**
	 * ユーザーエージェント名を設定
	 *
	 * @access public
	 * @param string $name ユーザーエージェント名
	 */
	public function setName ($name) {
		$this['name'] = $name;
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
	 * バージョンを返す
	 *
	 * @access public
	 * @return string バージョン
	 */
	public function getVersion () {
		return $this['version'];
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
				__CLASS__,
				(int)$this->hasSupport('html5_video'),
				(int)$this->hasSupport('html5_audio'),
				(int)$this->hasSupport('flash'),
				(int)$this->hasSupport('cookie'),
				(int)$this->hasSupport('attach_file'),
				(int)$this->isMobile(),
				(int)$this->isSmartPhone(),
				(int)$this->isTablet(),
			]);
		}
		return $this->digest;
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
		return addslashes($name);
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
		return BSArray::create();
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
	 * 規定のエンコードを返す
	 *
	 * @access public
	 * @return string 規定のエンコード
	 */
	public function getDefaultEncoding () {
		return 'utf8';
	}

	/**
	 * アサインすべき値を返す
	 *
	 * @access public
	 * @return mixed アサインすべき値
	 */
	public function assign () {
		$values = $this->getParameters();
		$values['supports'] = $this->supports;
		$values['bugs'] = $this->bugs;
		return $values;
	}

	static private function getTypes () {
		return BSArray::create([
			'WindowsPhone',
			'Tasman',
			'Trident',
			'Gecko',
			'Android',
			'iOS',
			'WebKit',
			'Presto',
			'LegacyMozilla',
			'Docomo',
			'Au',
			'SoftBank',
			'Console',
			'Default',
		]);
	}
}

