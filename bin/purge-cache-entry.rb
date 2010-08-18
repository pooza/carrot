#!/usr/local/bin/ruby -Ku

# mod_disk_cacheの古いキャッシュをパージ
#
# @package org.carrot-framework
# @author 小石達也 <tkoishi@b-shock.co.jp>
# @version $Id$

PATTERN = '/home/*/proxy/*'
APACHE_PREFIX = '/usr/local'
LIMIT = '512M'

Dir.glob(PATTERN).each do |path|
  system(APACHE_PREFIX + '/apache2/bin/htcacheclean -n -t -p' + path + ' -l' + LIMIT)
end