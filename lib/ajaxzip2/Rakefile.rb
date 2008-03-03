#!/usr/bin/env rake

# 郵便番号辞書のアップデート手順タスク
#
# @package jp.co.b-shock.carrot
# @author 小石達也 <tkoishi@b-shock.co.jp>
# @version $Id: Rakefile.rb 82 2007-11-03 17:51:31Z pooza $

$KCODE = 'u'

task :default => :all

desc '郵便番号辞書の作成'
task :all => ['data/ken_all.csv'] do
  sh 'perl ./csv2jsonzip.pl ./data/ken_all.csv'
  system('rm data/*.lzh data/*.csv')
end

desc '郵便番号辞書を削除'
task :clean do
  system('rm data/*.json data/*.lzh data/*.csv')
end

desc '郵便番号辞書の更新'
task :refresh => [:clean, :all]

file 'data/ken_all.csv' => ['data/ken_all.lzh'] do
  sh 'cd data; lha x ken_all.lzh'
end

file 'data/ken_all.lzh' do
  sh 'cd data; wget http://www.post.japanpost.jp/zipcode/dl/kogaki/lzh/ken_all.lzh'
end
