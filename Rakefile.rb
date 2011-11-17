#!/usr/bin/env rake

# carrotユーティリティタスク
#
# @package org.carrot-framework
# @author 小石達也 <tkoishi@b-shock.co.jp>

$KCODE = 'u'

ROOT_DIR = File.dirname(File.expand_path(__FILE__))
$LOAD_PATH.push(ROOT_DIR + '/lib/ruby')

require 'yaml'
require 'carrot/constants'
require 'carrot/environment'
require 'webapp/config/Rakefile.local'

desc 'インストールを実行'
task :install => [
  'var:init',
  'environment:init',
  'local:init',
]

desc 'テストを実行'
task :test =>['var:classes:clean'] do
  uid = Constants.new['BS_APP_PROCESS_UID']
  sh 'sudo -u ' + Constants.new['BS_APP_PROCESS_UID'] + ' bin/carrotctl.php -a Test'
end

namespace :database do
  desc 'データベースを初期化'
  task :init => ['local:database:init']
end

namespace :environment do
  task :init => [
    'file:init',
  ]

  namespace :file do
    desc 'サーバ環境設定ファイルを初期化'
    task :init => [
      Environment.file_path,
    ]

    file Environment.file_path do
      sh 'touch ' + Environment.file_path
    end
  end
end

namespace :var do
  desc 'varディレクトリを初期化'
  task :init => [
    :chmod,
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
      system 'sudo rm -R var/output/*'
    end
  end

  namespace :images do
    namespace :cache do
      desc 'イメージキャッシュをクリア'
      task :clean do
        system 'sudo rm -R var/image_cache/*'
      end
    end
  end

  namespace :css do
    desc 'cssキャッシュをクリア'
    task :clean do
      system 'sudo rm var/css_cache/*'
    end
  end

  namespace :js do
    desc 'jsキャッシュをクリア'
    task :clean do
      system 'sudo rm var/js_cache/*'
    end
  end

  namespace :classes do
    desc 'クラスヒント情報をクリア'
    task :clean do
      system 'sudo rm var/serialized/BSClassLoader.*'
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
        File.delete(path) if is_delete
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
  desc 'PHPDocumentorを実行'
  task :build do
    sh 'phpdoc -d lib/carrot,webapp/lib -t share/man -o HTML:Smarty:HandS'
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
