server 'aura.ellune.net', :app, :web, :db, :primary => true

set :domain, "korobi.io"
set :deploy_to, "/data/web/korobi.io"
set :branch, "www1-stable"
set :parameters_file, "/data/web/_resources/korobi/production.yml"
