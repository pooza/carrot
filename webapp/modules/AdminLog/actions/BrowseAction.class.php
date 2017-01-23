<?php
/**
 * Browseアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage AdminLog
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BrowseAction extends BSAction {
	private $exception;

	/**
	 * タイトルを返す
	 *
	 * @access public
	 * @return string タイトル
	 */
	public function getTitle () {
		return '管理ログ';
	}

	public function execute () {
		$this->request->setAttribute('dates', $this->getModule()->getDates());
		$entries = new BSArray;
		$keyword = $this->request['key'];
		foreach ($this->getModule()->getEntries() as $entry) {
			if (BSString::isBlank($keyword)
				|| BSString::isContain($keyword, $entry['message'])
				|| ($keyword == $entry['remote_host'])
			) {
				$entries[] = $entry;
			}
		}
		$this->request->setAttribute('entries', $entries);
		return BSView::SUCCESS;
	}

	public function handleError () {
		$this->request->setAttribute('dates', []);
		$entry = [
			'exception' => true,
			'date' => BSDate::getNow('Y-m-d H:i:s'),
			'remote_host' => $this->request->getHost()->getName(),
			'message' => 'ログを取得できません。',
		];
		if ($this->exception) {
			$message = new BSStringFormat('[%s] %s');
			$message[] = get_class($this->exception);
			$message[] = $this->exception->getMessage();
			$entry['message'] = $message->getContents();
		}
		$this->request->setAttribute('entries', [$entry]);
		return BSView::SUCCESS;
	}

	public function validate () {
		try {
			return !!$this->getModule()->getLogger();
		} catch (BSLogException $e) {
			$this->exception = $e;
			return false;
		}
	}
}

/* vim:set tabstop=4: */
