server 'chaos.ellune.net', :app, :web, :db, :primary => true

set :domain, "dev.korobi.io"
set :deploy_to, "/data/a/capifony/dev.korobi.io"
set :branch, "master"
set :parameters_file, "/data/a/capifony/resources/korobi/staging.yml"
set :controllers_to_clear, []
