#!/usr/bin/env rake

# carrotユーティリティタスク
#
# @package jp.co.b-shock.carrot
# @author 小石達也 <tkoishi@b-shock.co.jp>
# @version $Id$

$KCODE = 'u'

desc '運用環境の構築（productionと同じ）'
task :install => :production

desc '運用環境の構築'
task :production => [:chmod_var, 'www/awstats']

desc 'テスト環境の構築'
task :development => [:chmod_var, :svn_pset, 'www/doc']

desc 'varディレクトリを書き込み可に'
task :chmod_var do
  sh 'chmod -R 777 var/*'
end

desc '全ファイルのsvn属性を設定'
task :svn_pset do
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
  system 'svn pset svn:executable ON lib/awstats/awstats.pl'
end

desc 'varディレクトリ内の一時ファイルを削除'
task :clean do
  sh 'sudo rm -R var/*/*'
end

desc 'APIドキュメントへのシンボリックリンクを公開領域に'
file 'www/doc' do
  sh 'ln -s ../var/doc www/doc'
  sh 'svn pset svn:ignore doc www'
end

desc 'AWStatsレポートへのシンボリックリンクを公開領域に'
file 'www/awstats' do
  sh 'ln -s ../lib/awstats www/awstats'
  sh 'ln -s ../var/awstats_report www/awstats_report'
  sh 'svn pset svn:ignore awstats* www'
end

def media_types
  return {
    'conf' => '',
    'css' => 'text/css',
    'csv' => 'application/csv',
    'dat' => '',
    'gif' => 'image/gif',
    'htm' => 'text/html',
    'html' => 'text/html',
    'ini' => '',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'js' => 'text/javascript',
    'php' => '',
    'pl' => '',
    'pm' => '',
    'png' => 'image/png',
    'rb' => '',
    'sql' => '',
    'swf' => 'application/x-shockwave-flash',
    'tpl' => '',
    'ttf' => '',
    'txt' => 'text/plain',
    'xml' => 'application/xml',
  }
end
