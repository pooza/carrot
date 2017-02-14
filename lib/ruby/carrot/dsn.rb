# DSN
#
# @package jp.co.b-shock.carrot
# @author 小石達也 <tkoishi@b-shock.co.jp>

require 'carrot/constants'

module Carrot
  class DSN
    def initialize (name = 'default')
      @name = name
      @dsn = Carrot::Constants.new["BS_PDO_#{name}_DSN"]
      dsn = @dsn.split(':')
      @scheme = dsn.first
      @db = dsn[1].sub('%BS_VAR_DIR%', File.join(ROOT_DIR, 'var'))
    end

    def install
      raise "invalid scheme: #{@scheme}" unless installable?
      system('sudo', 'rm', @db) if File.exist?(@db)
      system("sqlite3 #{@db} < #{schema_file}")
      system('chmod', '666', @db)
    end

    private
    def installable?
      return @scheme == 'sqlite'
    end

    def schema_file
      ['_init', ''].each do |suffix|
        ['.sqlite.sql', '.sql'].each do |extension|
          path = File.join(ROOT_DIR, "share/sql/#{@name.downcase}#{suffix}#{extension}")
          return path if File.exist?(path)
        end
      end
      raise 'invalid schema file'
    end
  end
end
