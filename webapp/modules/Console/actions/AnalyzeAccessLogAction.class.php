<?php
/**
 * AnalyzeAccessLogアクション
 *
 * @package org.carrot-framework
 * @subpackage Console
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class AnalyzeAccessLogAction extends BSAction {
	private $config;
	private $prev;

	/**
	 * 設定値を返す
	 *
	 * @access private
	 * @return BSArray 設定値
	 */
	private function getAWStatsConfig () {
		if (!$this->config) {
			$this->config = new BSArray;
			$this->config['server_name'] = $this->controller->getHost()->getName();
			$this->config['server_name_aliases'] = BS_AWSTATS_SERVER_NAME_ALIASES;
			$this->config['awstat_data_dir'] = $this->controller->getPath('awstats_data');
			$this->config['awstat_dir'] = $this->controller->getPath('awstats');

			$networks = new BSArray;
			foreach (BSAdministratorRole::getInstance()->getAllowedNetworks() as $network) {
				$networks[] = sprintf(
					'%s-%s',
					$network->getAttribute('network'),
					$network->getAttribute('broadcast')
				);
			}
			$this->config['admin_networks'] = $networks->implode(' ');

			if (BS_AWSTATS_DAILY) {
				$this->config['logfile'] = BS_AWSTATS_LOG_DIR
					. '/%YYYY/%MM/access_%YYYY%MM%DD.log';
			} else {
				$this->config['logfile'] = BS_AWSTATS_LOG_FILE;
			}
		}
		return $this->config;
	}

	/**
	 * 解析を実行する
	 *
	 * @access private
	 * @param BSFile $file 対象ファイル
	 */
	private function analyze (BSFile $file = null) {
		$command = new BSCommandLine('awstats.pl');
		$command->setDirectory($this->controller->getDirectory('awstats'));
		$command->addValue('-config=awstats.conf');

		if ($file) {
			$command->addValue('-logfile=' . $file->getPath());
		}

		$command->addValue('-update');
		$command->execute();

		if ($command->hasError()) {
			throw new BSConsoleException($command->getResult());
		}
	}

	/**
	 * 昨日のアクセスログを返す
	 *
	 * @access private
	 * @return BSFile 昨日のアクセスログ
	 */
	private function getPrevLogFile () {
		if (!$this->prev && BS_AWSTATS_DAILY) {
			if ($dir = $this->controller->getDirectory('awstats_log')) {
				$yesterday = BSDate::getNow()->setAttribute('day', '-1');
				if ($dir = $dir->getEntry($yesterday->format('Y'))) {
					if ($dir = $dir->getEntry($yesterday->format('m'))) {
						$dir->setDefaultSuffix('.log');
						$name = 'access_' . $yesterday->format('Ymd');
						if ($file = $dir->getEntry($name)) {
							$this->prev = $file;
						}
					}
				}
			}
		}
		return $this->prev;
	}

	/**
	 * 設定ファイルを更新
	 *
	 * @access private
	 */
	private function updateConfig () {
		$smarty = new BSSmarty;
		$smarty->setTemplate('awstats.conf');
		$smarty->setAttribute('config', $this->getAWStatsConfig());
		$file = $this->controller->getDirectory('tmp')->createEntry('awstats.conf');
		$file->setContents($smarty->getContents());
	}

	public function execute () {
		try {
			$this->updateConfig();
			if ($file = $this->getPrevLogFile()) {
				$this->analyze($file);
			}
			$this->analyze();
		} catch (Exception $e) {
		}
		$this->controller->putLog('実行しました。', $this);
		return BSView::NONE;
	}
}

/* vim:set tabstop=4: */

