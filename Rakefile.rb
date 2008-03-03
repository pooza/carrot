#!/usr/bin/env rake

# carrotユーティリティタスク
#
# @package jp.co.b-shock.carrot
# @author 小石達也 <tkoishi@b-shock.co.jp>
# @version $Id$

$KCODE = 'u'
require 'webapp/config/Rakefile.local'

desc '運用環境の構築（productionと同じ）'
task :install => :production

desc '運用環境の構築'
task :production => [:chmod_var, :awstats, :ajaxzip2, :production_local]

desc 'テスト環境の構築'
task :development => [:chmod_var, :phpdoc, :ajaxzip2, :development_local]

desc 'varディレクトリを書き込み可に'
task :chmod_var do
  sh 'chmod -R 777 var/*'
end

desc '全ファイルのsvn属性を設定'
task :pset do
  system 'svn pset svn:ignore \'*\' var/*'
  media_types.each do |extension, type|
    if type != ''
      system 'svn pset svn:mime-type ' + type + ' `find . -name \'*.' + extension + '\'`'
    else
      system 'svn pdel svn:mime-type `find . -name \'*.' + extension + '\'`'
    end
    system 'svn pdel svn:executable `find . -name \'*.' + extension + '\'`'
  end
  system 'svn pset svn:executable ON bin/*'
end

desc 'varディレクトリ内の一時ファイルを削除'
task :clean do
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
  sh 'ln -s ../../var/cache/awstats.conf lib/AWStats/awstats.conf'
end

desc 'ajaxzip2を有効に'
task :ajaxzip2 => ['www/js/ajaxzip2/data', 'lib/ajaxzip2/data'] do
  system 'svn pset svn:executable ON lib/ajaxzip2/csv2jsonzip.pl'
end

file 'www/js/ajaxzip2/data' do
  sh 'ln -s ../../../var/zipcode www/js/ajaxzip2/data'
end

file 'lib/ajaxzip2/data' do
  sh 'ln -s ../../var/zipcode lib/ajaxzip2/data'
end

def media_types
  return {
    'conf' => '',
    'css' => 'text/css',
    'csv' => 'text/plain',
    'dat' => 'application/octet-stream',
    'gif' => 'image/gif',
    'htm' => 'text/html',
    'html' => 'text/html',
    'ini' => '',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'js' => 'text/javascript',
    'json' => 'application/json',
    'pdf' => 'application/pdf',
    'php' => '',
    'pl' => '',
    'pm' => '',
    'png' => 'image/png',
    'rb' => '',
    'sql' => '',
    'swf' => 'application/x-shockwave-flash',
    'tpl' => '',
    'ttf' => 'application/x-truetype-font',
    'txt' => 'text/plain',
    'xml' => 'application/xml',
  }
end
