git-app-test
============

This application wraps and manage a gitolite-powered git server. It tries to mimics Github's features but is intended to be used *behind a firewall* (some things are not that secure - eg: blob hotlinking). It was first developped to demonstrate the power of Git over Subversion at my work, while waiting for a [Github Enterprise](https://enterprise.github.com/) Licence (because Github rocks). Its also some kind of self-research about latest web development frameworks like [AngularJS](http://angularjs.org/).

Its a full-featured Git management console:
* Create, Fork and Delete repositories
* Admin access rights on repositories
* Create and Remove users
* Indexes revisions (so we can search through them)
* Browse repositories with style (Angular-powered UI)

This is far from complete and things might be buggy. Feel free to send a PR ;)

Installation
============

This application has several steps to be completed before you can enjoy using it at its best. This is because it depends on two major things: Gitolite and Apache (or any php-cgi compatible http daemon) running as the git user. This isn't trival because this involves advanced configuration skills and advanced unix knowledge (suexec is a pain in the a**).


Overview
--------

* Install [Git](http://git-scm.com/) + git [UNIX user](http://www.linuxmanpages.com/man8/useradd.8.php) + [Gitolite](http://gitolite.com/)
* Install Application
* Create [Apache](http://httpd.apache.org) VirtualHost and [git-http-backend (via Gitolite)](http://gitolite.com/gitolite/http.html)
* Configures Application
* Enjoy !

Create and Install Git + git UNIX user + Gitolite
-------------------------------------------------

* Git (and git-core) can be installed using your favourite package manager (apt, aptitude, yum ...) or via source code.
* Create unix user 'git': ```useradd -m -d /home/git git``` 
* SU to the new git user and go to HOME_DIR: ```su - git```

We will now install [Gitolite](http://gitolite.com/) and configures the Application as the main administrator, so we'll perform every installation steps as our new *git* user. The first step is mandatory: [READ THE WHOLE GITOLITE INSTALL GUIDE](http://gitolite.com/gitolite/master-toc.html) (twice if needed). You'll also need to chose a name for your application. It'll be *forgery* for the rest of this guide:

* [Generates a SSH-key](https://help.github.com/articles/generating-ssh-keys) for the git user (without passphrase): ```ssh-keygen -t rsa -C "forgery@nitronet.org"``` and save it to *HOME_DIR/username.pub* (eg: /home/git/forgery.pub)
* Clone Gitolite: ```git clone git://github.com/sitaramc/gitolite``` 
* [Set it up](http://gitolite.com/gitolite/qi.html): ```gitolite/install -ln``` 
* Install: ```gitolite setup -pk forgery.pub```
 
At this step, your server should be ready to use (ssh only) and from your git user. If not, read the [Gitolite installation guide](http://gitolite.com/gitolite/master-toc.html) again...


Install the App
---------------

Now that our git server is up and running, we'll want to install the application in order to administer it, create some repos, users. Here it'll be installed in /home/git/www:

* Clone this repository: ```git clone git://github.com/neiluJ/git-app-test www && cd www``` 
* Install [Composer](http://getcomposer.org/): ```curl -sS https://getcomposer.org/installer | php```
* Install dependencies: ```php composer.phar install -o```
* Install MySQL database: (TODO @see docs/ folder)

We'll now configure Apache before we can try and test it out.

Apache VirtualHost
------------------

You'll need to have Apache and PHP 5.3 running as CGI (with [fcgid](http://httpd.apache.org/mod_fcgid/) or [FastCGI](http://www.fastcgi.com/mod_fastcgi/docs/mod_fastcgi.html)) configured with [mod_suexec](http://httpd.apache.org/docs/2.2/en/mod/mod_suexec.html). It was easier with [suPHP](www.suphp.org) but haven't succeeded in making smart-http work along... 

Here is my final VirtualHost file:

``` apache
<VirtualHost *:80>
    ServerName        git.localhost
    ServerAdmin       julien@nitronet.org

    SetEnv GIT_PROJECT_ROOT /home/git/repositories
    SuexecUserGroup git git
    ScriptAlias /git/ /var/www/bin/gitolite-suexec-wrapper.sh/
    ScriptAlias /gitmob/ /var/www/bin/gitolite-suexec-wrapper.sh/
    SetEnv GITOLITE_HTTP_HOME /home/git
    SetEnv GIT_HTTP_EXPORT_ALL	

    DocumentRoot /home/git/www/public
    <Directory /home/git/www/public>
        Options       All
        AllowOverride All
        Order         allow,deny
        Allow         from all
    </Directory>

    <Location /git>
        AuthType Basic
        AuthName "Git Access"
        Require valid-user
        AuthUserFile /home/git/htpasswd.git
    </Location>
</VirtualHost>
``` 

Tweak it the way you want to fit your needs. What is VERY important is the: ```SuexecUserGroup git git``` making the app run with correct privileges to access repositories. ```Location /git``` and other directives are required to use the  smart-http mode and the ```/gitmob``` alias is for accessing repositories without having a user/password prompt. 

The ```AuthUserFile``` is a file managed by the application. You'll never have to edit/create it manually and you'll be able to configure its location in the config file.

My wrapper file ```/var/www/bin/gitolite-suexec-wrapper.sh/``` contents:

``` bash
#!/bin/bash
#
# Suexec wrapper for gitolite-shell
#

export GIT_PROJECT_ROOT="/home/git/repositories"
export GITOLITE_HTTP_HOME="/home/git"

exec ${GITOLITE_HTTP_HOME}/gitolite/src/gitolite-shell
```

IMPORTANT: Permissions and ownership of this file must be 0700 git:git !

App Configuration
-----------------

