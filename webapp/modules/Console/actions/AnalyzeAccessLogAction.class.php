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
	private $config;

	/**
	 * 設定値を返す
	 *
	 * @access private
	 * @return BSArray 設定値
	 */
	private function getConfig () {
		if (!$this->config) {
			$this->config = new BSArray;
			$this->config['server_name'] = $this->controller->getServerHost()->getName();
			$this->config['server_name_aliases'] = BS_AWSTATS_SERVER_NAME_ALIASES;
			$this->config['awstat_data_dir'] = $this->controller->getPath('awstats_data');
			$this->config['awstat_dir'] = $this->controller->getPath('awstats');

			$networks = new BSArray;
			foreach (BSAdministrator::getAllowedNetworks() as $network) {
				$networks[] = sprintf(
					'%s-%s',
					$network->getAttribute('network'),
					$network->getAttribute('broadcast')
				);
			}
			$this->config['admin_networks'] = $networks->implode(' ');

			if ($this->isDaily()) {
				$this->config['logfile'] = BS_AWSTATS_LOG_DIR
					. '/%%YYYY/%%MM/access_%%YYYY%%MM%%DD.log';
			} else {
				$this->config['logfile'] = BS_AWSTATS_LOG_FILE;
			}
		}
		return $this->config;
	}

	/**
	 * 日次モードか？
	 *
	 * @access private
	 * @return boolean 日次モードならTrue
	 */
	private function isDaily () {
		return defined('BS_AWSTATS_DAILY') && BS_AWSTATS_DAILY;
	}

	/**
	 * 解析を実行する
	 *
	 * @access private
	 * @param BSFile $file 対象ファイル
	 */
	private function analyze (BSFile $file = null) {
		$command = new BSArray;
		$command[] = $this->controller->getPath('awstats') . '/awstats.pl';
		$command[] = '-config=awstats.conf';

		if ($file) {
			$command[] = '-logfile=' . $file->getPath();
		}

		$command[] = '-update';
		shell_exec($command->join(' '));
	}

	public function execute () {
		$this->controller->setMemoryLimit('128M');
		$smarty = new BSSmarty();
		$smarty->setTemplate('awstats.conf');
		$smarty->setAttribute('config', $this->getConfig());
		$file = $this->controller->getDirectory('cache')->createEntry('awstats.conf');
		$file->setContents($smarty->getContents());

		if ($this->isDaily()) {
			$dir = new BSDirectory(BS_AWSTATS_LOG_DIR);
			$yesterday = BSDate::getNow()->setAttribute('day', '-1');

			if ($dir = $dir->getEntry($yesterday->format('Y'))) {
				if ($dir = $dir->getEntry($yesterday->format('m'))) {
					$dir->setDefaultSuffix('.log');
					$name = 'access_' . $yesterday->format('Ymd');
					if ($file = $dir->getEntry($name)) {
						$this->analyze($file);
						$file->compress();
					}
				}
			}
		}
		$this->analyze();

		BSLog::put(get_class($this) . 'を実行しました。');
		return View::NONE;
	}
}

/* vim:set tabstop=4 ai: */
?>
