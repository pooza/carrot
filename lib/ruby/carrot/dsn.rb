#!/usr/bin/env ruby

# DSN
#
# @package org.carrot-framework
# @author 小石達也 <tkoishi@b-shock.co.jp>

require 'rubygems'
require 'rake'
require 'carrot/constants'

class DSN
  include FileUtils

  def initialize (name)
    @name = name
    @dsn = Constants.new['BS_PDO_' + name + '_DSN']
    dsn = @dsn.split(':')
    @scheme = dsn[0]
    @db = dsn[1].sub!('%BS_VAR_DIR%', ROOT_DIR + '/var')
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
        path = ROOT_DIR + '/share/sql/' + @name.downcase + suffix + extension
        if File.exists?(path)
          return path
        end
      end
    end
    raise 'invalid schema file'
  end
end
