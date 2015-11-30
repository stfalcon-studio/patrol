set :application, "xn--80awkfjh8d.com"
set :domain,      "144.76.238.50"
set :deploy_to,   "/var/www/xn--80awkfjh8d.com"
set :app_path,    "app"

set :repository,  "git@git.stfalcon.com:dorozhnyj-patrul.git"
set :branch,      "master"

set :scm,         :git
set :git_enable_submodules, 1

role :web,        domain, :primary => true       # Your HTTP server, Apache/etc
role :app,        domain, :primary => true       # This may be the same as your `Web` server
role :db,         domain, :primary => true       # This is where Rails migrations will run

default_run_options[:pty] = true 

set :keep_releases, 3
set :user,          "dorozhnyj-patrul"
set :use_sudo,      false

set :use_composer,  true
#set :deploy_via,    :rsync_with_remote_cache

set :shared_files,        ["app/config/parameters.yml"]
set :shared_children,     [app_path + "/logs", web_path + "/uploads"]
set :dump_assetic_assets, true

after "deploy:update_code" do
  deploy.migrate
end

