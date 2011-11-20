#!/usr/bin/env ruby -Ku

# 昨日分のアクセスログをgzip圧縮
#
# @package org.carrot-framework
# @author 小石達也 <tkoishi@b-shock.co.jp>

GZIP_CMD = '/usr/bin/gzip'
LOG_DIR = '/home/*/logs'

require 'date'

date = Date.today - 1
command = GZIP_CMD + ' ' + LOG_DIR + '/*/' + date.strftime('%Y/%m') \
  + '/*_' + date.strftime('%Y%m%d') + '.log'
system(command)