#!/usr/bin/env rake

# carrotユーティリティタスク
#
# @package jp.co.b-shock.carrot
# @author 小石達也 <tkoishi@b-shock.co.jp>
# @version $Id: Rakefile.rb 365 2007-07-24 15:55:31Z pooza $

$KCODE = 'u'

desc '運用環境の構築（productionと同じ）'
task :install => :production

desc '運用環境の構築'
task :production => [:chmod_var]

desc 'テスト環境の構築'
task :development => [:install, :svn_pset, 'www/doc']

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
