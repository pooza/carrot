<?php
/**
 * AnalyzeAccessLogアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Console
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class AnalyzeAccessLogAction extends BSAction {

	/**
	 * 設定値を返す
	 *
	 * @access private
	 * @return string[] 設定値の配列
	 */
	private function getConfig () {
		$networks = array();
		foreach (BSAdministrator::getAllowedNetworks() as $network) {
			$networks[] = sprintf(
				'%s-%s',
				$network->getAttribute('network'),
				$network->getAttribute('broadcast')
			);
		}

		return array(
			'logfile' => BS_AWSTATS_LOG_FILE,
			'server_name' => $this->controller->getServerHost()->getName(),
			'server_name_aliases' => BS_AWSTATS_SERVER_NAME_ALIASES,
			'awstat_data_dir' => $this->controller->getPath('awstats_data'),
			'awstat_dir' => $this->controller->getPath('awstats'),
			'admin_networks' => implode(' ', $networks),
		);
	}

	public function execute () {
		$smarty = new BSSmarty();
		$smarty->setTemplate('awstats.conf');
		$smarty->setAttribute('config', $this->getConfig());

		$file = $this->controller->getDirectory('cache')->createEntry('awstats.conf');
		$file->putLine($smarty->getContents());
		$file->close();

		$command = sprintf(
			'%s/awstats.pl -config=awstats.conf -update',
			$this->controller->getPath('awstats')
		);
		shell_exec($command);

		BSLog::put(get_class($this) . 'を実行しました。');
		return View::NONE;
	}
}

/* vim:set tabstop=4 ai: */
?>