server 'chaos.ellune.net', :app, :web, :db, :primary => true

set :domain, "korobi.io"
set :deploy_to, "/data/a/capifony/korobi.io"
set :branch, "www1-stable"
set :parameters_file, "/data/a/capifony/resources/korobi/production.yml"
