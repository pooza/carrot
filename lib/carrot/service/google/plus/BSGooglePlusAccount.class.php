<?php
/**
 * @package org.carrot-framework
 * @subpackage service.google.plus
 */

/**
 * Google+アカウント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSGooglePlusAccount
	implements BSImageContainer, BSSerializable, BSAssignable, BSHTTPRedirector {

	protected $id;
	protected $url;
	protected $profile;
	protected $activities;
	protected $digest;
	protected $service;
	protected $imageURL;

	/**
	 * @access public
	 * @param string $id ユーザーID
	 */
	public function __construct ($id) {
		$this->id = (string)$id;

		if (!$this->getSerialized()) {
			$this->serialize();
		}
		$serialized = $this->getSerialized();

		$this->profile = BSArray::create($serialized['profile']);
		$this->activities = BSArray::create($serialized['activities']);
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
	}

	/**
	 * サービスへの接続を返す
	 *
	 * @access protected
	 * @return BSTwitterService サービス
	 */
	protected function getService () {
		if (!$this->service) {
			$this->service = new BSGooglePlusService;
		}
		return $this->service;
	}

	/**
	 * 最近のアクティビティを返す
	 *
	 * @access public
	 * @return BSArray 最近のアクティビティ
	 */
	public function getActivities () {
		return $this->activities;
	}

	/**
	 * プロフィールアイコン画像を返す
	 *
	 * @access public
	 * @return BSImage プロフィールアイコン画像
	 */
	public function getIcon () {
		try {
			$image = new BSImage;
			$image->setImage($this->getImageURL()->fetch());
			$image->setType(BSMIMEType::getType('png'));
			return $image;
		} catch (BSHTTPException $e) {
			return null;
		} catch (BSImageException $e) {
			return null;
		}
	}

	/**
	 * キャッシュをクリア
	 *
	 * @access public
	 * @param string $size
	 */
	public function clearImageCache ($size = null) {
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
			$images = new BSImageManager;
			$info = $images->getImageInfo($file, $size, $pixel, $flags);
			$info['alt'] = $this->getName();
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
		$dir = BSFileUtility::getDirectory('google_account');
		if ($file = $dir->getEntry($this->getImageFileBaseName($size), 'BSImageFile')) {
			$date = BSDate::getNow()->setParameter('hour', '-1');
			if (!$file->getUpdateDate()->isPast($date)) {
				return $file;
			}
			$file->clearImageCache($size);
			$file->delete();
		}

		if (!$icon = $this->getIcon()) {
			return null;
		}
		$file = BSFileUtility::createTemporaryFile('.png', 'BSImageFile');
		$file->setEngine($icon);
		$file->save();
		$file->setName($this->getImageFileBaseName($size));
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
		throw new BSServiceException($this . 'の画像ファイルを設定できません。');
	}

	/**
	 * 画像ファイルベース名を返す
	 *
	 * @access public
	 * @param string $size サイズ名
	 * @return string 画像ファイルベース名
	 */
	public function getImageFileBaseName ($size) {
		return sprintf('%s_%s', $this->getID(), $size);
	}

	/**
	 * 画像URLを返す
	 *
	 * @access public
	 * @return BSHTTPURL URL
	 */
	public function getImageURL () {
		if (!$this->imageURL) {
			$this->imageURL = BSURL::create($this->profile['image']['url']);
			$this->imageURL->setParameter('sz', 512);
		}
		return $this->imageURL;
	}

	/**
	 * アカウントIDを返す
	 *
	 * @access public
	 * @return string ID
	 */
	public function getID () {
		return (string)$this->id;
	}

	/**
	 * スクリーン名を返す
	 *
	 * @access public
	 * @return string スクリーン名
	 */
	public function getName () {
		return $this->profile['displayName'];
	}

	/**
	 * コンテナのラベルを返す
	 *
	 * @access public
	 * @param string $language 言語
	 * @return string ラベル
	 */
	public function getLabel ($language = 'ja') {
		return sprintf(
			'%s %s',
			$this->profile['name']['familyName'],
			$this->profile['name']['givenName']
		);
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
				$this->getID(),
			]);
		}
		return $this->digest;
	}

	/**
	 * シリアライズ
	 *
	 * @access public
	 */
	public function serialize () {
		$values = new BSArray;

		try {
			$response = $this->getService()->sendGET('/plus/v1/people/' . $this->getID());
			$json = new BSJSONRenderer;
			$json->setContents($response->getRenderer()->getContents());
			$values['profile'] = $json->getResult();
		} catch (BSException $e) {
			throw new BSGooglePlusException('プロフィールが取得できません。');
		}

		try {
			$response = $this->getService()->sendGET(
				'/plus/v1/people/' . $this->getID() . '/activities/public'
			);
			$json = new BSJSONRenderer;
			$json->setContents($response->getRenderer()->getContents());
			$values['activities'] = $json->getResult();
		} catch (BSException $e) {
			throw new BSGooglePlusException('アクティビティが取得できません。');
		}

		BSController::getInstance()->setAttribute($this, $values);
	}

	/**
	 * シリアライズ時の値を返す
	 *
	 * @access public
	 * @return mixed シリアライズ時の値
	 */
	public function getSerialized () {
		$date = BSDate::getNow()->setParameter('minute', '-' . BS_SERVICE_GOOGLE_PLUS_MINUTES);
		return BSController::getInstance()->getAttribute($this, $date);
	}

	/**
	 * アサインすべき値を返す
	 *
	 * @access public
	 * @return mixed アサインすべき値
	 */
	public function getAssignableValues () {
		$values = clone $this->profile;
		$values['activities'] = $this->activities;
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
			$this->url = BSURL::create($this->profile['url']);
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
		return $this->getURL()->redirect();
	}

	/**
	 * URLをクローンして返す
	 *
	 * @access public
	 * @return BSURL
	 */
	public function createURL () {
		return clone $this->getURL();
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('Google+アカウント "%s"', $this->getID());
	}
}

/* vim:set tabstop=4: */
