#!/usr/local/bin/ruby -Ku

# mod_disk_cacheの古いキャッシュをパージ
#
# @package org.carrot-framework
# @author 小石達也 <tkoishi@b-shock.co.jp>
# @version $Id$

PATTERN = '/home/*/proxy/*'
COMMAND = '/usr/local/apache2/bin/htcacheclean'
LIMIT = '512M'

Dir.glob(PATTERN).each do |path|
  system(COMMAND + ' -n -t -p' + path + ' -l' + LIMIT)
end