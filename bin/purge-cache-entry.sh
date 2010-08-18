#!/bin/sh

# mod_disk_cacheの古いキャッシュをパージ
#
# @package org.carrot-framework
# @author 小石達也 <tkoishi@b-shock.co.jp>
# @version $Id$

for user in minc
do
  for site in www.mincs.info blog.mincs.info
  do
    /usr/local/apache2/bin/htcacheclean -n -t -p/home/$user/proxy/$site -l512M
  done
done