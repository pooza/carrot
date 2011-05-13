<?php
/**
 * AnalyzeAccessLogアクション
 *
 * @package org.carrot-framework
 * @subpackage Console
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class AnalyzeAccessLogAction extends BSAction {
	protected $awstatsConfig;
	private $prev;

	private function getAWStatsConfig () {
		if (!$this->awstatsConfig) {
			$this->awstatsConfig = new BSArray;
			$this->awstatsConfig['server_name'] = $this->controller->getHost()->getName();
			$this->awstatsConfig['server_name_aliases'] = BS_AWSTATS_SERVER_NAME_ALIASES;
			$this->awstatsConfig['awstat_data_dir'] = BSFileUtility::getPath('awstats_data');
			$this->awstatsConfig['awstat_dir'] = BSFileUtility::getPath('awstats');

			$networks = new BSArray;
			foreach (BSAdministratorRole::getInstance()->getAllowedNetworks() as $network) {
				$networks[] = sprintf(
					'%s-%s',
					$network->getAddress(),
					$network->getBroadcastAddress()
				);
			}
			$this->awstatsConfig['admin_networks'] = $networks->join(' ');

			if (BS_AWSTATS_DAILY) {
				$this->awstatsConfig['logfile'] = BS_AWSTATS_LOG_DIR
					. '/%YYYY/%MM/access_%YYYY%MM%DD.log';
			} else {
				$this->awstatsConfig['logfile'] = BS_AWSTATS_LOG_FILE;
			}
		}
		return $this->awstatsConfig;
	}

	private function analyze (BSFile $file = null) {
		$command = new BSCommandLine('awstats.pl');
		$command->setDirectory(BSFileUtility::getDirectory('awstats'));
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

	private function getPrevLogFile () {
		if (!$this->prev && BS_AWSTATS_DAILY) {
			if ($dir = BSFileUtility::getDirectory('awstats_log')) {
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

	private function updateConfig () {
		$smarty = new BSSmarty;
		$smarty->setTemplate('awstats.conf');
		$smarty->setAttribute('config', $this->getAWStatsConfig());
		$file = BSFileUtility::getDirectory('tmp')->createEntry('awstats.conf');
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
		BSLogManager::getInstance()->put('実行しました。', $this);
		return BSView::NONE;
	}
}

/* vim:set tabstop=4: */
