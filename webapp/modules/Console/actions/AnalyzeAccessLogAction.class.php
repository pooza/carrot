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
	public function execute () {
		$config = array(
			'logfile' => BS_AWSTATS_LOG_FILE,
			'server_name' => $this->controller->getServerHost()->getName(),
			'server_name_aliases' => BS_AWSTATS_SERVER_NAME_ALIASES,
			'awstat_data_dir' => $this->controller->getPath('awstats_data'),
			'awstat_dir' => $this->controller->getPath('awstats')
		);
		$smarty = new BSSmarty();
		$smarty->setTemplate('awstats.conf');
		$smarty->setAttribute('config', $config);

		$file = $this->controller->getDirectory('tmp')->createEntry('awstats.conf');
		$file->putLine($smarty->getContents());
		$file->close();

		$command = sprintf(
			'cd %s; ./awstats.pl -config=awstats.conf -output > %s/index.html',
			$this->controller->getPath('awstats'),
			$this->controller->getPath('awstats_report')
		);
		shell_exec($command);
	}
}

/* vim:set tabstop=4 ai: */
?>