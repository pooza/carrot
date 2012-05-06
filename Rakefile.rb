#!/usr/bin/env rake

# carrotユーティリティタスク
#
# @package org.carrot-framework
# @author 小石達也 <tkoishi@b-shock.co.jp>

$KCODE = 'u'

ROOT_DIR = File.dirname(File.expand_path(__FILE__))
$LOAD_PATH.push(ROOT_DIR + '/lib/ruby')

require 'yaml'
require 'shellwords'
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
  cmd = 'sudo -u ' + Constants.new['BS_APP_PROCESS_UID'] + ' bin/carrotctl.php -a Test'
  cmd += ' -i ' + Shellwords.shellescape(ARGV[1]) if ARGV[1]
  sh cmd
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
      system 'sudo rm var/serialized/BSLoader.*'
    end
  end

  namespace :config do
    desc '設定キャッシュをクリア'
    task :clean do
      system 'sudo rm -R var/config_cache/*'
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
