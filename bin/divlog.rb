#!/usr/bin/env ruby

# /root/binに置き、以下の様に設定して使う。
#
# LogFormat "%V %h %l %u %t \"%r\" %>s %b \"%{Referer}i\" \"%{User-Agent}i\"" combined_vhost
# CustomLog "|/root/bin/divlog.rb" combined_vhost

require 'fileutils'

SITES_DIR = '/usr/local/www/apache24/data/'
LOG_DIR = '/var/log/httpd/'

ARGF.each do |line|
  entry = line.split(' ')
  domain = entry.shift
  next unless Dir.exist?(SITES_DIR + domain)

  path = LOG_DIR + domain + '/' + Time.now.strftime('%Y/%m/access_%Y%m%d') + '.log'
  path_dir = File.dirname(path)
  FileUtils.mkdir_p(path_dir) unless Dir.exist?(path_dir)

  File.open(path, 'a') do |file|
    file.puts entry.join(' ')
  end
end
