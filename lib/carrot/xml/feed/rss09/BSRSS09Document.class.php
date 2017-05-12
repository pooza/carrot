<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage xml.feed.rss09
 */

/**
 * RSS0.9x文書
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSRSS09Document extends BSXMLDocument implements BSFeedDocument {
	protected $version = '0.9';

	/**
	 * @access public
	 * @param string $name 要素の名前
	 */
	public function __construct ($name = null) {
		parent::__construct('rss');
		$this->setAttribute('version', $this->version);
		$this->setDate(BSDate::getNow());
		$this->getChannel()->createElement('generator', $this->controller->getName());
		$author = BSAuthorRole::getInstance();
		$this->setAuthor($author->getName('ja'), $author->getMailAddress('ja'));
	}

	/**
	 * エントリー要素の名前を返す
	 *
	 * @access public
	 * @return string
	 */
	public function getEntryElementName () {
		return 'item';
	}

	/**
	 * エントリー要素要素の格納先を返す
	 *
	 * @access public
	 * @return BSXMLElement
	 */
	public function getEntryRootElement () {
		return $this->getChannel();
	}

	/**
	 * 妥当な文書か？
	 *
	 * @access public
	 * @return boolean 妥当な文書ならTrue
	 */
	public function validate () {
		return (parent::validate()
			&& $this->query('/rss/channel/title')
			&& $this->query('/rss/channel/description')
			&& $this->query('/rss/channel/link')
			&& $this->query('/rss/channel/language')
		);
	}

	/**
	 * チャンネル要素を返す
	 *
	 * @access public
	 * @return BSXMLElement チャンネル要素
	 */
	public function getChannel () {
		if (!$element = $this->getElement('channel')) {
			$element = $this->createElement('channel');
		}
		return $element;
	}

	/**
	 * タイトルを返す
	 *
	 * @access public
	 * @return string タイトル
	 */
	public function getTitle () {
		if ($element = $this->getChannel()->getElement('title')) {
			return $element->getBody();
		}
	}

	/**
	 * タイトルを設定
	 *
	 * @access public
	 * @param string $title タイトル
	 */
	public function setTitle ($title) {
		if (!$element = $this->getChannel()->getElement('title')) {
			$element = $this->getChannel()->createElement('title');
		}
		$element->setBody($title);
	}

	/**
	 * ディスクリプションを設定
	 *
	 * @access public
	 * @param string $description ディスクリプション
	 */
	public function setDescription ($description) {
		if (!$element = $this->getChannel()->getElement('description')) {
			$element = $this->getChannel()->createElement('description');
		}
		$element->setBody($description);
	}

	/**
	 * リンクを返す
	 *
	 * @access public
	 * @return BSHTTPURL リンク
	 */
	public function getLink () {
		if ($element = $this->getChannel()->getElement('link')) {
			return BSURL::create($element->getBody());
		}
	}

	/**
	 * リンクを設定
	 *
	 * @access public
	 * @param BSHTTPRedirector $link リンク
	 */
	public function setLink (BSHTTPRedirector $link) {
		if (!$element = $this->getChannel()->getElement('link')) {
			$element = $this->getChannel()->createElement('link');
		}
		$element->setBody($link->getURL()->getContents());
	}

	/**
	 * オーサーを設定
	 *
	 * @access public
	 * @param string $name 名前
	 * @param BSMailAddress $email メールアドレス
	 */
	public function setAuthor ($name, BSMailAddress $email = null) {
		if (!$element = $this->getChannel()->getElement('managingEditor')) {
			$element = $this->getChannel()->createElement('managingEditor');
		}
		if ($email) {
			$element->setBody(sprintf('%s (%s)', $email->getContents(), $name));
		} else {
			$element->setBody($name);
		}
	}

	/**
	 * 日付を返す
	 *
	 * @access public
	 * @return BSDate 日付
	 */
	public function getDate () {
		if ($element = $this->getChannel()->getElement('lastBuildDate')) {
			return BSDate::create($element->getBody());
		}
	}

	/**
	 * 日付を設定
	 *
	 * @access public
	 * @param BSDate $date 日付
	 */
	public function setDate (BSDate $date) {
		if (!$element = $this->getChannel()->getElement('lastBuildDate')) {
			$element = $this->getChannel()->createElement('lastBuildDate');
		}
		$element->setBody($date->format(DateTime::RSS));
	}

	/**
	 * エントリーを生成して返す
	 *
	 * @access public
	 * @return BSFeedEntry エントリー
	 */
	public function createEntry () {
		return BSFeedUtility::createEntry($this);
	}

	/**
	 * Zend形式のフィードオブジェクトを変換
	 *
	 * @access public
	 * @param Zend_Feed_Abstract $feed 変換対象
	 * @return BSFeedDocument
	 */
	public function convert (Zend_Feed_Abstract $feed) {
		$this->setTitle($feed->title());
		foreach ($feed as $entry) {
			try {
				$element = $this->createEntry();
				$element->setTitle($entry->title());
				if ($values = new BSArray($entry->link())) {
					if (!is_string($url = $values[0]) && isset($url->firstChild)) {
						$url = $url->firstChild->wholeText;
					}
					$element->setLink(BSURL::create($url));
				}
				if ($values = new BSArray($entry->pubDate())) {
					$element->setDate(BSDate::create($values[0]));
				}
			} catch (Exception $e) {
			}
		}
	}

	/**
	 * エントリーのタイトルを配列で返す
	 *
	 * @access public
	 * @return BSArray
	 */
	public function getEntryTitles () {
		return BSFeedUtility::getEntryTitles($this);
	}
}

