# carrotユーティリティタスク
#
# @package jp.co.b-shock.carrot
# @author 小石達也 <tkoishi@b-shock.co.jp>

ROOT_DIR = File.expand_path('..', __FILE__)
$LOAD_PATH.push(File.join(ROOT_DIR, 'lib/ruby'))

require 'carrot/constants'
require 'carrot/environment'
require 'carrot/deployer'
require 'carrot/periodic_creator'
require 'carrot/rsyslog_util'
require File.join(ROOT_DIR, 'webapp/config/Rakefile.local')

desc 'インストール'
task :install => [
  'var:init',
  'environment:init',
  'htdocs:init',
  'periodic:init',
  'rsyslog:init',
  'var:classes:clean',
  'local:init',
]

desc 'アンインストール'
task :uninstall => [
  'htdocs:clean',
  'periodic:clean',
  'rsyslog:clean',
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
    desc 'サーバ環境設定ファイルを作成'
    task :init => [
      Carrot::Environment.file_path,
    ]

    file Carrot::Environment.file_path do
      sh "touch #{Carrot::Environment.file_path}"
    end
  end
end

namespace :rsyslog do
  task :init => [:clean, :create]

  desc 'rsyslog設定をクリア'
  task :clean do
    Carrot::RsyslogUtil.clean
  end

  desc 'rsyslog設定を作成'
  task :create do
    Carrot::RsyslogUtil.create
  end
end

namespace :htdocs do
  task :init => [:clean, :create]

  desc 'htdocsをクリア'
  task :clean do
    Carrot::Deployer.clean
  end

  desc 'htdocsにリンクを作成'
  task :create do
    Carrot::Deployer.create
  end
end

namespace :periodic do
  desc 'periodicを初期化'
  task :init => [:clean, :daily, :hourly, :frequently]

  desc 'periodicをクリア'
  task :clean do
    Carrot::PeriodicCreator.clean
  end

  [:daily, :hourly, :frequently].each do |period|
    desc "periodic #{period}を登録"
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
    'css:clean',
    'js:clean',
    'images:cache:clean',
  ]

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
      sh 'sudo rm -R var/css_cache/*'
    end
  end

  namespace :js do
    desc 'jsキャッシュをクリア'
    task :clean do
      sh 'sudo rm -R var/js_cache/*'
    end
  end

  namespace :render do
    desc 'renderキャッシュをクリア'
    task :clean do
      sh 'sudo rm -R var/output/*'
    end
  end

  namespace :classes do
    desc 'クラスヒント情報をクリア'
    task :clean do
      sh 'rm var/serialized/BSLoader.json'
    end
  end
end
