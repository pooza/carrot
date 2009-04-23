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
  task :init => ['var:init', 'database:init', 'local:init']
end

namespace :database do
  desc 'データベースを初期化'
  task :init => ['local:init']
end

namespace :var do
  desc 'varディレクトリを初期化'
  task :init => [:chmod, :clean]

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
  desc 'PHPDocumentorを初期化'
  task :init => ['www/doc']

  file 'www/doc' do
    sh 'ln -s ../var/doc www/doc'
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
  task :init => ['www/carrotlib/js/ajaxzip2/data', 'lib/ajaxzip2/data'] do
    system 'svn pset svn:executable ON lib/ajaxzip2/csv2jsonzip.pl'
    sh 'cd lib/ajaxzip2; rake all'
  end

  file 'www/carrotlib/js/ajaxzip2/data' do
    sh 'ln -s ../../../../var/zipcode www/carrotlib/js/ajaxzip2/data'
  end

  file 'lib/ajaxzip2/data' do
    sh 'ln -s ../../var/zipcode lib/ajaxzip2/data'
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
