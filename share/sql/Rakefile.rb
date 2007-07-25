#!/usr/bin/env rake

# データベース初期化タスク
#
# @package jp.co.b-shock.carrot
# @author 小石達也 <tkoishi@b-shock.co.jp>
# @version $Id: Rakefile.rb 359 2007-07-14 12:12:10Z pooza $

$KCODE = 'u'

MYSQLDUMP = 'mysqldump'
DBHOST = 'localhost'
DBUSER = 'root'
DBPASS = ''
DBNAME = ''

task :default => :all

desc '全てのSQLファイルを生成'
task :all => ['schema.sql', 'init.sql']

desc '全てのSQLファイルを削除'
task :clean do
  sh 'rm *.sql'
end

desc 'schema.sql'
file 'schema.sql' do
  sh dump_command_line + ' --no-data > schema.sql'
end

desc 'init.sql'
file 'init.sql' do
  sh dump_command_line + ' > init.sql'
end

def dump_command_line
  command_line = []
  command_line.push(MYSQLDUMP)
  command_line.push('-h' + DBHOST)
  command_line.push('-u' + DBUSER)
  if DBPASS.to_s != ''
    command_line.push('-p' + DBPASS)
  end
  command_line.push(dbname)
  return command_line.join(' ')
end

def dbname
  if DBNAME.to_s == ''
    return File.basename(File.dirname(File.dirname(File.dirname(__FILE__))))
  else
    return DBNAME
  end
end
