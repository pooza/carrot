#!/usr/bin/env rake

# データベースダンプ生成タスク
#
# @package jp.co.b-shock.carrot
# @author 小石達也 <tkoishi@b-shock.co.jp>
# @version $Id$

$KCODE = 'u'
ROOT_DIR = File.dirname(File.dirname(File.dirname(File.expand_path(__FILE__))))

task :default => :all

desc '全てのSQLファイルを生成'
task :all => ['schema.sql', 'init.sql']

desc '全てのSQLファイルを削除'
task :clean do
  sh 'rm *.sql'
end

desc 'schema.sql'
file 'schema.sql' do
  sh ROOT_DIR + '/bin/carrotctl.php -a CreateDatabaseSchema'
end

desc 'init.sql'
file 'init.sql' do
  sh ROOT_DIR + '/bin/carrotctl.php -a CreateDatabaseDump'
end
