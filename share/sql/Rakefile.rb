#!/usr/bin/env rake

# データベースダンプ生成タスク
#
# @package jp.co.b-shock.carrot
# @author 小石達也 <tkoishi@b-shock.co.jp>
# @version $Id$

$KCODE = 'u'

task :default => :all

desc '全てのSQLファイルを生成'
task :all => ['schema.sql', 'init.sql']

desc '全てのSQLファイルを削除'
task :clean do
  system 'rm *.sql'
end

desc '全てのSQLファイルを再度作成'
task :refresh => [:clean, :all]

file 'schema.sql' do
  sh '../../bin/carrotctl.php -a CreateDatabaseSchema'
end

file 'init.sql' do
  sh '../../bin/carrotctl.php -a CreateDatabaseDump'
end
