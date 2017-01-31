# carrotユーティリティタスク
#
# @package jp.co.b-shock.carrot
# @author 小石達也 <tkoishi@b-shock.co.jp>

ROOT_DIR = File.expand_path('..', __FILE__)
$LOAD_PATH.push(File.join(ROOT_DIR, 'lib/ruby'))

require 'carrot/constants'
require 'carrot/environment'
require 'carrot/periodic_creator'
require 'carrot/rsyslog_util'
require File.join(ROOT_DIR, 'webapp/config/Rakefile.local')

desc 'インストールを実行'
task :install => [
  'var:init',
  'environment:init',
  'rsyslog:init',
  'local:init',
]

desc 'テストを実行'
task :test =>['var:classes:clean'] do
  sh "sudo -u #{Carrot::Constants.new['BS_APP_PROCESS_UID']} bin/carrotctl.php -a Test"
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
      Carrot::Environment.file_path,
    ]

    file Carrot::Environment.file_path do
      sh "touch #{Carrot::Environment.file_path}"
    end
  end
end

namespace :rsyslog do
  task :init => [
    'config:init',
  ]

  namespace :config do
    desc 'rsyslog設定ファイルを設置'
    task :init do
      Carrot::RsyslogUtil.create_config_file
    end
  end
end

namespace :periodic do
  desc 'periodicを登録'
  task :init => [:daily]

  [:daily, :hourly, :frequently].each do |period|
    task period do
      periodic = Carrot::PeriodicCreator.new
      periodic[:period] = period
      periodic.create
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
    'output:clean',
    'css:clean',
    'js:clean',
    'images:cache:clean',
  ]

  namespace :output do
    desc 'レンダーキャッシュをクリア'
    task :clean do
      sh 'sudo rm -R var/output/*'
    end
  end

  namespace :images do
    namespace :cache do
      desc 'イメージキャッシュをクリア'
      task :clean do
        sh 'sudo rm -R var/image_cache/*'
      end
    end
  end

  namespace :css do
    desc 'cssキャッシュをクリア'
    task :clean do
      sh 'sudo rm var/css_cache/*'
    end
  end

  namespace :js do
    desc 'jsキャッシュをクリア'
    task :clean do
      sh 'sudo rm var/js_cache/*'
    end
  end

  namespace :classes do
    desc 'クラスヒント情報をクリア'
    task :clean do
      sh 'touch webapp/config/constant'
    end
  end
end
