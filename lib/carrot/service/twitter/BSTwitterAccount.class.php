<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage service.twitter
 */

/**
 * Twitterアカウント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSTwitterAccount implements BSImageContainer, BSHTTPRedirector {
	use BSHTTPRedirectorMethods;
	protected $name;
	protected $url;
	private $service;

	/**
	 * @access public
	 * @param mixed $name スクリーンネーム
	 */
	public function __construct ($name) {
		$this->service = new BSTwitterService;
		$this->name = $name;
	}

	/**
	 * Twitterサービスを返す
	 *
	 * @access public
	 * @return BSTwitterService Twitterサービス
	 */
	public function getService () {
		return $this->service;
	}

	/**
	 * つぶやく
	 *
	 * @access public
	 * @param string $message メッセージ
	 * @return BSJSONRenderer 結果文書
	 */
	public function tweet ($message) {
		if ($message instanceof BSStringFormat) {
			$message = $message->getContents();
		}
		$query = new BSWWWFormRenderer;
		$query['status'] = $message;
		$response = $this->getService()->sendPOST('/1.1/statuses/update.json', $query);
		$json = new BSJSONRenderer;
		$json->setContents($response->getRenderer()->getContents());
		return $json;
	}

	/**
	 * ダイレクトメッセージを送る
	 *
	 * @access public
	 * @param string $message メッセージ
	 * @param BSTwitterAccount $account 宛先アカウント
	 * @return BSJSONRenderer 結果文書
	 */
	public function sendDirectMessage ($message, BSTwitterAccount $account) {
		if ($message instanceof BSStringFormat) {
			$message = $message->getContents();
		}
		$query = new BSWWWFormRenderer;
		$query['screen_name'] = $account->getName();
		$query['text'] = $message;
		$response = $this->getService()->sendPOST('/1.1/direct_messages/new.json', $query);
		$json = new BSJSONRenderer;
		$json->setContents($response->getRenderer()->getContents());
		return $json;
	}

	/**
	 * ダイレクトメッセージを送る
	 *
	 * @access public
	 * @param string $message メッセージ
	 * @param BSTwitterAccount $account 宛先アカウント
	 * @return BSJSONRenderer 結果文書
	 * @final
	 */
	final public function sendDM ($message, BSTwitterAccount $account) {
		return $this->sendDirectMessage($message, $account);
	}

	/**
	 * タイムラインを返す
	 *
	 * @access public
	 * @param integer $count ツイート数
	 * @return BSArray タイムライン
	 */
	public function getTimeline ($count = 10) {
		return $this->service->getTimeline($this->name, $count);
	}

	/**
	 * プロフィールを返す
	 *
	 * @access public
	 * @return BSArray プロフィール
	 */
	public function getProfile () {
		return $this->service->getProfile($this->name);
	}

	/**
	 * プロフィールアイコン画像を返す
	 *
	 * @access public
	 * @return BSImage プロフィールアイコン画像
	 */
	public function getIcon () {
		try {
			$url = BSURL::create($this->getProfile()['profile_image_url_https']);
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
	 * キャッシュをクリア
	 *
	 * @access public
	 * @param string $size
	 */
	public function removeImageCache ($size) {
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
	public function getImageInfo ($size, $pixel = null, $flags = null) {
		if ($file = $this->getImageFile()) {
			$images = new BSImageManager;
			$info = $images->getImageInfo($file, $size, $pixel, $flags);
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
	public function getImageFile ($size) {
		$dir = BSFileUtility::getDirectory('twitter_account');
		if ($file = $dir->getEntry($this->getImageFileBaseName($size), 'BSImageFile')) {
			$date = BSDate::getNow();
			$date['minute'] = '-' + BS_SERVICE_TWITTER_MINUTES;
			if (!$file->getUpdateDate()->isPast($date)) {
				return $file;
			}
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
	 * 画像ファイルベース名を返す
	 *
	 * @access public
	 * @param string $size サイズ名
	 * @return string 画像ファイルベース名
	 */
	public function getImageFileBaseName ($size) {
		return sprintf('%010d_%s', $this->getID(), $size);
	}

	/**
	 * アカウントIDを返す
	 *
	 * @access public
	 * @return integer ID
	 */
	public function getID () {
		return (int)$this->getProfile()['id'];
	}

	/**
	 * スクリーン名を返す
	 *
	 * @access public
	 * @return string スクリーン名
	 */
	public function getName () {
		return $this->getProfile()['screen_name'];
	}

	/**
	 * コンテナのラベルを返す
	 *
	 * @access public
	 * @param string $language 言語
	 * @return string ラベル
	 */
	public function getLabel ($language = 'ja') {
		return $this->getName();
	}

	/**
	 * リダイレクト対象
	 *
	 * @access public
	 * @return BSURL
	 */
	public function getURL () {
		if (!$this->url) {
			$this->url = BSURL::create();
			$this->url['scheme'] = 'https';
			$this->url['host'] = 'twitter.com';
			$this->url['path'] = '/' . $this->name;
		}
		return $this->url;
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('Twitterアカウント "%s"', $this->name);
	}
}

