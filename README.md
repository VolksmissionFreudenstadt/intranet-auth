intranet-auth
=============

Authenticates Apache's `authnz-external` against a kOOL database.
This is helpful, as newer versions of Apache do not work with mod_auth_mysql anymore and mod_auth_dbm cannot handle kOOL's md5-encoded password fields.

## Prerequisites
### Install and enable `authnz-external`. 

On Ubuntu 16.04, you'd do:

    apt-get install libapache2-mod-authnz-external
    a2enmod authnz-external
    
## Configure the script
* Copy the script and the included file `auth.yaml.dist` to a directory in your server.
* Make sure `intranet-auth.php` is executable by your apache user.
* Rename `auth.yaml.dist` to `auth.yaml` and fill in your database configuration.

## Configure your VirtualHost

Add the following configuration to your VirtualHost declaration:

    <VirtualHost *:80>
    	
        [...]
    
        AddExternalAuth intranet /path/to/intranet-auth.php
        SetExternalAuthMethod intranet pipe
    
        <Location /your-location>
                AuthType Basic
                AuthName "Describe your secure area"
                AuthBasicProvider external
                AuthExternal intranet
                Require valid-user
        </Location>
    
    </VirtualHost>
    

    