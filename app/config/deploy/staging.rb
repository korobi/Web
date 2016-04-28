server 'aura.miserable.ninja', :app, :web, :db, :primary => true

set :domain, "dev.korobi.io"
set :deploy_to, "/data/web/dev.korobi.io"
set :branch, "master"
set :parameters_file, "/data/web/_resources/korobi/staging.yml"
set :controllers_to_clear, []
