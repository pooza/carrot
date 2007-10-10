#!/usr/bin/env rake

# データベースダンプ生成タスク
#
# @package jp.co.b-shock.carrot
# @author 小石達也 <tkoishi@b-shock.co.jp>
# @version $Id$

ROOT_DIR = File.dirname(File.dirname(File.dirname(File.expand_path(__FILE__))))
$KCODE = 'u'
$LOAD_PATH.unshift(ROOT_DIR + '/lib/ruby')

# サーバ環境定義iniファイルを取得
require 'ini_file'
names = []
names.push(Socket.gethostname)
names.push(File.basename(ROOT_DIR) + '.' + Socket.gethostname)
names.push('localhost')
ini = nil
names.each do |name|
  path = ROOT_DIR + '/webapp/config/server/' + name + '.ini'
  if File.exist?(path)
    ini = IniFile.new(path)
    break
  end
end
ini.prefix = 'bs'

# DSNをパース
dsn = {}
ini.settings['BS_PDO_DSN'].split(/[:;]/).each do |param|
  param = param.split('=')
  dsn[param[0]] = param[1]
end

# コマンドラインを生成
command_line = []
command_line.push('mysqldump')
command_line.push('-h' + dsn['host'])
command_line.push('-u' + ini.settings['BS_PDO_UID'])
if ini.settings['BS_PDO_PASSWORD'] != ''
  command_line.push('-p' + ini.settings['BS_PDO_PASSWORD'])
end
command_line.push(dsn['dbname'])
command_line = command_line.join(' ')



task :default => :all

desc '全てのSQLファイルを生成'
task :all => ['schema.sql', 'init.sql']

desc '全てのSQLファイルを削除'
task :clean do
  sh 'rm *.sql'
end

desc 'schema.sql'
file 'schema.sql' do
  sh command_line + ' --no-data > schema.sql'
end

desc 'init.sql'
file 'init.sql' do
  sh command_line + ' > init.sql'
end
