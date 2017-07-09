<?php
/**
 * Summaryアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage AdminMemcache
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class SummaryAction extends BSAction {

	/**
	 * タイトルを返す
	 *
	 * @access public
	 * @return string タイトル
	 */
	public function getTitle () {
		return 'Memcacheの状態';
	}

	public function execute () {
		$manager = BSMemcacheManager::getInstance();
		$this->request->setAttribute('servers', BSArray::create());
		foreach ($manager->getServerNames() as $name) {
			if ($server = BSMemcacheManager::getInstance()->getServer($name)) {
				$this->request->getAttribute('servers')[$name] = $server->getAttributes();
			}
		}
		return BSView::SUCCESS;
	}
}

