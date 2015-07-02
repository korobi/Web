set :stages, %w(production staging)
set :default_stage, "staging"
set :stage_dir, "app/config/deploy"

require 'capistrano/ext/multistage'

set :use_composer, true
set :composer_bin, "/usr/local/bin/composer"

set :application, "korobi"
set :app_path, "app"
set :dump_assetic_assets, true
set :writable_dirs, ["app/cache", "app/logs", "app/sessions"]
set :permission_method, :chmod_alt
set :use_set_permissions, true
set :webserver_user, "www-data"
set :user, "deploy"
set :interactive_mode, false
set :use_sudo, false

set :repository, "git@github.com:korobi/Web.git"
set :scm, :git
set :deploy_via, :remote_cache
set :keep_releases, 3

set :model_manager, "doctrine"

before 'symfony:composer:install', 'upload_parameters'

task :upload_parameters do
  origin_file = parameters_file if parameters_file
  if origin_file
    relative_path = "app/config/parameters.yml"

    if shared_files && shared_files.include?(relative_path)
      destination_file = shared_path + "/" + relative_path
    else
      destination_file = latest_release + "/" + relative_path
    end
    try_sudo "mkdir -p #{File.dirname(destination_file)}"

    run "cp #{origin_file} #{destination_file}"
  end
end
