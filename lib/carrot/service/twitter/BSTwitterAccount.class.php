<?php
/**
 * @package org.carrot-framework
 * @subpackage service.twitter
 */

/**
 * Twitterアカウント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSTwitterAccount
	implements BSImageContainer, BSSerializable, BSAssignable, BSHTTPRedirector {

	protected $id;
	protected $url;
	protected $profile;
	protected $tweets;
	static private $service;

	/**
	 * @access public
	 * @param mixed $id ユーザーID,スクリーンネーム等
	 */
	public function __construct ($id) {
		$this->id = $id;
		if (!$this->getSerialized()) {
			$this->serialize();
		}

		$this->tweets = new BSArray;
		foreach ((array)$this->getSerialized() as $entry) {
			$tweet = new BSArray($entry);
			if (!$this->profile) {
				$this->profile = new BSArray($tweet['user']);
			}

			$url = BSURL::getInstance();
			$url['host'] = BSTwitterService::DEFAULT_HOST;
			$url['path'] = '/' . $this->getScreenName() . '/status/' . $entry['id'];
			$tweet['url'] = $url->getContents();
			$this->tweets[] = $tweet;
		}
	}

	/**
	 * @access public
	 * @param string $method メソッド名
	 * @param mixed[] $values 引数
	 */
	public function __call ($method, $values) {
		if (mb_ereg('^get([[:upper:]][[:alnum:]]+)$', $method, $matches)) {
			$name = BSString::underscorize($matches[1]);
			if (!BSString::isBlank($this->profile[$name])) {
				return $this->profile[$name];
			}
		} 
		$message = new BSStringFormat('仮想メソッド"%s"は未定義です。');
		$message[] = $method;
		throw new BadFunctionCallException($message);
	}

	/**
	 * 最近のつぶやきを返す
	 *
	 * @access public
	 * @return BSArray 最近のつぶやき
	 */
	public function getTweets () {
		return $this->tweets;
	}

	/**
	 * つぶやく
	 *
	 * @access public
	 * @param string $message メッセージ
	 * @return BSJSONRenderer 結果文書
	 */
	public function tweet ($message) {
		$response = self::getService()->sendPostRequest(
			'/statuses/update' . BS_SERVICE_TWITTER_SUFFIX,
			new BSArray(array('status' => $message))
		);
		$json = new BSJSONRenderer;
		$json->setContents($response->getRenderer()->getContents());
		return $json;
	}

	/**
	 * プロフィールアイコン画像を返す
	 *
	 * @access public
	 * @return BSImage プロフィールアイコン画像
	 */
	public function getIcon () {
		try {
			$url = BSURL::getInstance($this->profile['profile_image_url']);
			$image = new BSImage;
			$image->setImage($url->fetch());
			$image->setType(BSMIMEType::getType('png'));
			return $image;
		} catch (BSHTTPException $e) {
			return null;
		} catch (BSImageException $e) {
			return null;
		}
	}

	/**
	 * 画像の情報を返す
	 *
	 * @access public
	 * @param string $size サイズ名
	 * @param integer $pixel ピクセルサイズ
	 * @param integer $flags フラグのビット列
	 * @return BSArray 画像の情報
	 */
	public function getImageInfo ($size = 'icon', $pixel = null, $flags = null) {
		if ($file = $this->getImageFile()) {
			$caches = BSImageCacheHandler::getInstance();
			$info = $caches->getImageInfo($file, $size, $pixel, $flags);
			$info['alt'] = $this->getLabel();
			return $info;
		}
	}

	/**
	 * 画像ファイルを返す
	 *
	 * @access public
	 * @param string $size サイズ名
	 * @return BSImageFile 画像ファイル
	 */
	public function getImageFile ($size = 'icon') {
		$dir = BSFileUtility::getDirectory('twitter_account');
		if ($file = $dir->getEntry($this->getImageFileBaseName(), 'BSImageFile')) {
			$date = BSDate::getNow()->setAttribute('hour', '-1');
			if (!$file->getUpdateDate()->isPast($date)) {
				return $file;
			}
			$file->clearImageCache($size);
			$file->delete();
		}

		if (!$icon = $this->getIcon()) {
			return null;
		}
		$file = BSFileUtility::getTemporaryFile('png', 'BSImageFile');
		$file->setEngine($icon);
		$file->save();
		$file->setName($this->getImageFileBaseName());
		$file->moveTo($dir);
		return $file;
	}

	/**
	 * 画像ファイルを設定する
	 *
	 * @access public
	 * @param BSImageFile $file 画像ファイル
	 * @param string $size サイズ名
	 */
	public function setImageFile (BSImageFile $file, $size = 'icon') {
		throw new BSImageException($this . 'の画像ファイルを設定できません。');
	}

	/**
	 * 画像ファイルベース名を返す
	 *
	 * @access public
	 * @param string $size サイズ名
	 * @return string 画像ファイルベース名
	 */
	public function getImageFileBaseName ($size = 'icon') {
		return sprintf('%010d_%s', $this->getID(), $size);
	}

	/**
	 * アカウントIDを返す
	 *
	 * @access public
	 * @return integer ID
	 */
	public function getID () {
		return (int)$this->profile['id'];
	}

	/**
	 * スクリーン名を返す
	 *
	 * @access public
	 * @return string スクリーン名
	 */
	public function getName () {
		return $this->profile['screen_name'];
	}

	/**
	 * コンテナのラベルを返す
	 *
	 * @access public
	 * @param string $language 言語
	 * @return string ラベル
	 */
	public function getLabel ($language = 'ja') {
		return $this->profile['name'];
	}

	/**
	 * 属性名へシリアライズ
	 *
	 * @access public
	 * @return string 属性名
	 */
	public function serializeName () {
		return get_class($this) . '.' . $this->id;
	}

	/**
	 * シリアライズ
	 *
	 * @access public
	 */
	public function serialize () {
		$response = self::getService()->sendGetRequest(
			'/statuses/user_timeline/' . $this->id . BS_SERVICE_TWITTER_SUFFIX
		);

		$json = new BSJSONRenderer;
		$json->setContents($response->getRenderer()->getContents());
		BSController::getInstance()->setAttribute($this, $json->getResult());
	}

	/**
	 * シリアライズ時の値を返す
	 *
	 * @access public
	 * @return mixed シリアライズ時の値
	 */
	public function getSerialized () {
		$date = BSDate::getNow()->setAttribute('minute', '-' . BS_SERVICE_TWITTER_MINUTES);
		return BSController::getInstance()->getAttribute($this, $date);
	}

	/**
	 * アサインすべき値を返す
	 *
	 * @access public
	 * @return mixed アサインすべき値
	 */
	public function getAssignValue () {
		$values = clone $this->profile;
		$values['timeline_url'] = $this->getURL()->getContents();
		$values['tweets'] = $this->tweets;
		return $values;
	}

	/**
	 * リダイレクト対象
	 *
	 * @access public
	 * @return BSURL
	 */
	public function getURL () {
		if (!$this->url) {
			$this->url = BSURL::getInstance();
			$this->url['host'] = BSTwitterService::DEFAULT_HOST;
			$this->url['path'] = '/' . $this->getScreenName();
		}
		return $this->url;
	}

	/**
	 * リダイレクト
	 *
	 * @access public
	 * @return string ビュー名
	 */
	public function redirect () {
		return BSController::getInstance()->redirect($this);
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('Twitterアカウント "%s"', $this->id);
	}

	/**
	 * サービスへの接続を返す
	 *
	 * @access public
	 * @return BSTwitterService サービス
	 */
	static protected function getService () {
		if (!self::$service) {
			self::$service = new BSTwitterService;
		}
		return self::$service;
	}
}

/* vim:set tabstop=4: */
