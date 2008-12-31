#!/usr/bin/env rake

# carrotユーティリティタスク
#
# @package org.carrot-framework
# @author 小石達也 <tkoishi@b-shock.co.jp>
# @version $Id$

$KCODE = 'u'
require 'webapp/config/Rakefile.local'

desc '運用環境の構築（productionと同じ）'
task :install => :production

desc '運用環境の構築'
task :production => [:chmod_var, :log, :pset, :awstats, :production_local]

desc 'テスト環境の構築'
task :development => [:chmod_var, :log, :pset, :phpdoc, :development_local]

desc 'varディレクトリを書き込み可に'
task :chmod_var do
  sh 'chmod -R 777 var/*'
end

desc 'ログデータベースを設定'
task :log => ['var/db/log.sqlite3']

file 'var/db/log.sqlite3' do
  sh 'sqlite3 var/db/log.sqlite3 < share/sql/log_schema.sql'
  sh 'chmod 666 var/db/log.sqlite3'
end

desc '全ファイルのsvn属性を設定'
task :pset do
  system 'svn pset svn:ignore \'*\' var/*'
  media_types.each do |extension, type|
    if type != nil
      system 'svn pset svn:mime-type ' + type + ' `find . -name \'*.' + extension + '\'`'
    else
      system 'svn pdel svn:mime-type `find . -name \'*.' + extension + '\'`'
    end
    system 'svn pdel svn:executable `find . -name \'*.' + extension + '\'`'
  end
  system 'svn pset svn:executable ON bin/*'
  system 'svn pset svn:executable ON lib/*/*.pl'
end

desc 'varディレクトリ内の一時ファイルを削除'
task :clear => :clean

desc 'varディレクトリ内の一時ファイルを削除'
task :clean => [:clean_var, :log]

task :clean_var do
  sh 'sudo rm -R var/*/*'
end

desc 'PHPDocumentorを有効に'
task :phpdoc => ['www/doc']

file 'www/doc' do
  sh 'ln -s ../var/doc www/doc'
end

desc 'AWStatsを有効に'
task :awstats => ['www/awstats', 'lib/AWStats/awstats.conf'] do
  system 'svn pset svn:executable ON lib/AWStats/awstats.pl'
end

file 'www/awstats' do
  sh 'ln -s ../lib/AWStats www/awstats'
end

file 'lib/AWStats/awstats.conf' do
  sh 'ln -s ../../var/tmp/awstats.conf lib/AWStats/awstats.conf'
end

desc 'ajaxzip2を有効に'
task :ajaxzip2 => ['www/carrotlib/js/ajaxzip2/data', 'lib/ajaxzip2/data'] do
  system 'svn pset svn:executable ON lib/ajaxzip2/csv2jsonzip.pl'
  sh 'cd lib/ajaxzip2; rake all'
end

file 'www/carrotlib/js/ajaxzip2/data' do
  sh 'ln -s ../../../../var/zipcode www/carrotlib/js/ajaxzip2/data'
end

file 'lib/ajaxzip2/data' do
  sh 'ln -s ../../var/zipcode lib/ajaxzip2/data'
end

def media_types
  require 'yaml'
  return YAML.load_file('webapp/config/mime/types.yaml')['types']
end
