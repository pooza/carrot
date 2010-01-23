#!/usr/bin/env rake

# carrotユーティリティタスク
#
# @package org.carrot-framework
# @author 小石達也 <tkoishi@b-shock.co.jp>
# @version $Id$

$KCODE = 'u'
require 'yaml'
require 'webapp/config/Rakefile.local'

namespace :production do
  desc '運用環境の構築'
  task :init => ['var:init', 'database:init', 'local:init']
end

namespace :development do
  desc '開発環境の構築'
  task :init => ['var:init', 'database:init', 'local:init', 'phpdoc:init']
end

namespace :database do
  desc 'データベースを初期化'
  task :init => ['local:init']
end

namespace :var do
  desc 'varディレクトリを初期化'
  task :init => [
    :chmod,
    :clean,
    'images:cache:init',
    'images:favicon:init',
    'css:init',
    'js:init',
  ]

  task :chmod do
    system 'chmod 777 var/*'
  end

  desc 'varディレクトリをクリア'
  task :clean do
    system 'sudo rm -R var/*/*'
  end

  namespace :images do
    namespace :cache do
      desc 'イメージキャッシュを初期化'
      task :init => ['www/carrotlib/images/cache']

      desc 'イメージキャッシュをクリア'
      task :clean do
        system 'rm -R var/image_cache/*'
      end

      file 'www/carrotlib/images/cache' do
        sh 'ln -s ../../../var/image_cache www/carrotlib/images/cache'
      end
    end

    namespace :favicon do
      desc 'faviconを初期化'
      task :init => ['www/carrotlib/images/favicon']

      file 'www/carrotlib/images/favicon' do
        sh 'ln -s ../../../var/favicon www/carrotlib/images/favicon'
      end
    end
  end

  namespace :css do
    desc 'cssキャッシュを初期化'
    task :init => ['www/carrotlib/css/cache']

    desc 'cssキャッシュをクリア'
    task :clean do
      system 'rm  var/css_cache/*'
    end

    file 'www/carrotlib/css/cache' do
      sh 'ln -s ../../../var/css_cache www/carrotlib/css/cache'
    end
  end

  namespace :js do
    desc 'jsキャッシュを初期化'
    task :init => ['www/carrotlib/js/cache']

    desc 'jsキャッシュをクリア'
    task :clean do
      system 'rm  var/js_cache/*'
    end

    file 'www/carrotlib/js/cache' do
      sh 'ln -s ../../../var/js_cache www/carrotlib/js/cache'
    end
  end

  namespace :classes do
    desc 'クラスファイルをリロード'
    task :init do
      system 'rm var/serialized/BSClassLoader.*'
    end
  end

  namespace :config do
    desc '設定キャッシュをクリア'
    task :clean do
      Dir.glob(File.expand_path('var/serialized/*')).each do |path|
        is_delete = true
        keep_types.each do |pattern|
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

    namespace :docomo_agents do
      desc 'DoCoMoの端末リストを取得'
      task :fetch do
        sh 'bin/makexmldocomomap.pl > webapp/config/docomo_agents.xml'
      end

      desc 'DoCoMoの端末リストを更新'
      task :update do
        sh 'svn update webapp/config/docomo_atents.xml'
      end
    end

    def keep_types
      types = []
      ['carrot', 'application'].each do |name|
        begin
          types += YAML.load_file('webapp/config/constant/' + name + '.yaml')['serialize']['keep']
        rescue
        end
      end
      return types
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
  task :init => ['www/awstats', 'lib/AWStats/awstats.conf'] do
    system 'svn pset svn:executable ON lib/AWStats/awstats.pl'
  end

  file 'www/awstats' do
    sh 'ln -s ../lib/AWStats www/awstats'
  end

  file 'lib/AWStats/awstats.conf' do
    sh 'ln -s ../../var/tmp/awstats.conf lib/AWStats/awstats.conf'
  end
end

namespace :ajaxzip2 do
  desc 'ajaxzip2を初期化'
  task :init => ['www/carrotlib/js/ajaxzip2/data', 'lib/ajaxzip2/data', :json, :clear_temp]

  desc '郵便番号辞書の更新'
  task :refresh => [:clear, :json, :clear_temp]

  desc '郵便番号辞書をクリア'
  task :clear => [:clear_temp, :clear_json]

  task :json => ['lib/ajaxzip2/data/ken_all.csv'] do
    sh 'cd lib/ajaxzip2; ./csv2jsonzip.pl data/ken_all.csv'
  end

  task :clear_temp do
    system 'rm lib/ajaxzip2/data/ken_all.*'
  end

  task :clear_json do
    system 'rm lib/ajaxzip2/data/*.json'
  end

  file 'www/carrotlib/js/ajaxzip2/data' do
    sh 'ln -s ../../../../var/zipcode www/carrotlib/js/ajaxzip2/data'
  end

  file 'lib/ajaxzip2/data' do
    sh 'ln -s ../../var/zipcode lib/ajaxzip2/data'
  end

  file 'lib/ajaxzip2/data/ken_all.csv' => ['lib/ajaxzip2/data/ken_all.lzh'] do
    sh 'cd lib/ajaxzip2/data; lha x ken_all.lzh'
  end

  file 'lib/ajaxzip2/data/ken_all.lzh' do
    sh 'cd lib/ajaxzip2/data; wget http://www.post.japanpost.jp/zipcode/dl/kogaki/lzh/ken_all.lzh'
  end
end

namespace :distribution do
  desc '全ファイルのsvn属性を設定'
  task :pset do
    system 'svn pset svn:ignore \'*\' var/*'
    media_types.each do |extension, type|
      if type != nil
        system 'svn pset svn:mime-type ' + type + ' `find . -name \'*.' + extension + '\'`'
      else
        system 'svn pdel svn:mime-type `find . -name \'*.' + extension + '\'`'
      end
      system 'svn pdel svn:executable `find . -name \'*.' + extension + '\'`'
    end
    system 'svn pset svn:executable ON bin/*'
    system 'svn pset svn:executable ON lib/*/*.pl'
    system 'cd share; svn pset svn:eol-style LF `find . -name \'*.as\'`'
  end

  desc '配布アーカイブを作成'
  task :archive do
    if repos_url == nil
      exit 1
    end
    export_dest = 'var/tmp/' + project_name
    sh 'svn export ' + repos_url + ' ' + export_dest
    system 'rm ' + export_dest + '/webapp/config/constant/*.local.yaml'
    sh 'cd ' + export_dest + '/..; tar cvzf ../tmp/' + project_name + '.tar.gz ' + project_name
    system 'rm -R ' + export_dest
  end

  def media_types
    return YAML.load_file('webapp/config/mime.yaml')['types']
  end

  def repos_url
    config = YAML.load_file('webapp/config/constant/application.yaml')
    begin
      return config['app']['svn']['url']
    rescue
      return nil
    end
  end

  def project_name
    return File.basename(File.dirname(__FILE__)).split('.')[0]
  end
end
