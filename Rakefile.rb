#!/usr/bin/env rake

# carrotユーティリティタスク
#
# @package org.carrot-framework
# @author 小石達也 <tkoishi@b-shock.co.jp>

$KCODE = 'u'
require 'yaml'
require 'webapp/config/Rakefile.local'

desc 'インストールを実行'
task :install => ['var:init', 'local:init']

desc 'テストを実行'
task :test do
  uid = Constants.new['BS_APP_PROCESS_UID']
  sh 'sudo -u ' + Constants.new['BS_APP_PROCESS_UID'] + ' bin/carrotctl.php -a Test'
end

namespace :database do
  desc 'データベースを初期化'
  task :init => ['local:database:init']
end

namespace :var do
  desc 'varディレクトリを初期化'
  task :init => [
    :chmod,
    'images:cache:init',
    'css:init',
    'js:init',
  ]

  task :chmod do
    sh 'chmod 777 var/*'
  end

  desc '各種キャッシュをクリア'
  task :clean => [
    'config:clean',
    'output:clean',
    'css:clean',
    'js:clean',
    'images:cache:clean',
  ]

  namespace :output do
    desc 'レンダーキャッシュをクリア'
    task :clean do
      sh 'rm -R var/output/*'
    end
  end

  namespace :images do
    namespace :cache do
      task :init => ['www/carrotlib/images/cache']

      desc 'イメージキャッシュをクリア'
      task :clean do
        sh 'rm -R var/image_cache/*'
      end

      file 'www/carrotlib/images/cache' do
        sh 'ln -s ../../../var/image_cache www/carrotlib/images/cache'
      end
    end
  end

  namespace :css do
    task :init => ['www/carrotlib/css/cache']

    desc 'cssキャッシュをクリア'
    task :clean do
      sh 'sudo rm var/css_cache/*'
    end

    file 'www/carrotlib/css/cache' do
      sh 'ln -s ../../../var/css_cache www/carrotlib/css/cache'
    end
  end

  namespace :js do
    task :init => ['www/carrotlib/js/cache']

    desc 'jsキャッシュをクリア'
    task :clean do
      sh 'sudo rm var/js_cache/*'
    end

    file 'www/carrotlib/js/cache' do
      sh 'ln -s ../../../var/js_cache www/carrotlib/js/cache'
    end
  end

  namespace :classes do
    desc 'クラスヒント情報をクリア'
    task :clean do
      sh 'sudo rm var/serialized/BSClassLoader.*'
    end
  end

  namespace :config do
    desc '設定キャッシュをクリア'
    task :clean do
      patterns = Constants.new['BS_SERIALIZE_KEEP']
      Dir.glob(File.expand_path('var/serialized/*')).each do |path|
        is_delete = true
        patterns.each do |pattern|
          if File.fnmatch?(pattern, File.basename(path))
            is_delete = false
            break
          end
        end
        if is_delete
          File.delete(path)
        end
      end
      system 'sudo rm var/cache/*'
    end

    desc '設定キャッシュを全てクリア'
    task :clean_all do
      system 'sudo rm var/cache/*'
      system 'sudo rm var/serialized/*'
    end
  end
end

namespace :phpdoc do
  desc 'PHPDocumentorを有効に'
  task :init => ['www/man']

  file 'www/man' do
    sh 'ln -s ../share/man www/man'
  end

  desc 'PHPDocumentorを実行'
  task :build do
    sh 'phpdoc -d lib/carrot,webapp/lib -t share/man -o HTML:Smarty:HandS'
  end
end

namespace :awstats do
  desc 'AWStatsを初期化'
  task :init => ['www/awstats', 'lib/AWStats/awstats.conf']

  file 'www/awstats' do
    sh 'ln -s ../lib/AWStats www/awstats'
  end

  file 'lib/AWStats/awstats.conf' do
    sh 'ln -s ../../var/tmp/awstats.conf lib/AWStats/awstats.conf'
  end
end

namespace :docomo do
  desc 'docomoの端末リストを取得'
  task :fetch do
    sh 'bin/makexmldocomomap.pl > webapp/config/docomo_agents.xml'
  end

  desc 'docomoの端末リストを取得'
  task :update => [:fetch]
end

namespace :svn do
  desc '全ファイルのsvn属性を設定'
  task :pset do
    system 'svn pset svn:ignore \'*\' var/*'
    media_types.each do |extension, type|
      extension_arg = '-name \'*.' + extension + '\''
      if type == nil
        system 'find . ' + extension_arg + ' | xargs svn pdel svn:mime-type'
      else
        system 'find . ' + extension_arg + ' | xargs svn pset svn:mime-type ' + type
      end
      if (type == nil) || (/^text\// =~ type)
        system 'find . ' + extension_arg + ' | xargs svn pset svn:eol-style LF'
      end
      system 'find . ' + extension_arg + ' | xargs svn pdel svn:executable'
    end
    ['pl', 'rb', 'cgi'].each do |extension|
      extension_arg = '-name \'*.' + extension + '\''
      system 'find lib ' + extension_arg + ' | xargs svn pset svn:executable ON'
    end
    ['pl', 'rb', 'php'].each do |extension|
      extension_arg = '-name \'*.' + extension + '\''
      system 'find bin ' + extension_arg + ' | xargs svn pset svn:executable ON'
    end
  end

  def media_types
    return YAML.load_file('webapp/config/mime.yaml')['types']
  end
end

class Constants
  def initialize
    @constants = Hash.new
    ['carrot', 'package', 'application', server_name].each do |name|
      begin
        path = 'webapp/config/constant/' + name + '.yaml';
        @constants.update(flatten('BS', YAML.load_file(path), '_'))
      rescue
      end
    end
  end

  def [] (name)
    return @constants[name.upcase]
  end

  def flatten (prefix, node, glue)
    contents = Hash.new
    if node.instance_of?(Hash)
      node.each do |key, value|
        key = prefix + glue + key
        contents.update(flatten(key, value, glue))
      end
    else
      contents[prefix.upcase] = node
    end
    return contents
  end

  def server_name
    return File.basename(File.dirname(File.expand_path(__FILE__)))
  end
end

class DSN
  def initialize (name)
    @name = name
    @dsn = Constants.new['BS_PDO_' + name + '_DSN']
    dsn = @dsn.split(':')
    @scheme = dsn[0]
    @db = dsn[1].sub!('%BS_VAR_DIR%', 'var')
  end

  def install
    raise 'invalid scheme: ' + @scheme if @scheme != 'sqlite'
    sh 'sudo rm ' + @db if File.exists?(@db)
    sh 'sqlite3 "' + @db + '" < ' + self.schema_file
    sh 'chmod 666 ' + @db
  end

  def schema_file
    ['_init', ''].each do |suffix|
      ['.sqlite.sql', '.sql'].each do |extension|
        path = 'share/sql/' + @name.downcase + suffix + extension
        if File.exists?(path)
          return path
        end
      end
    end
    raise 'invalid schema file'
  end
end
