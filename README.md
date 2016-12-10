# Homatisation

The purpose of this repo is to generate a simple front interface

## Install
### Installing dependencies
```bash
$ composer install
```
### Installing assets
```bash
$ bower install
```
## Create some dirs
```bash
$ mkdir var
$ mkdir var/logs
$ mkdir var/states
$ chmod 755 var/logs
$ chmod 755 var/states
```
### WebServer configuration
Using nginx:
```
    location / {
        try_files $uri /index.php$is_args$args;
    }
```

## Recipes
Put your recipes files in the recipes folder.
Check the test-recipe.yml file for sample configuration.
Put your images in the web/images folder.

### Test a recipe yml syntax
```bash
$ bin/yaml-lint recipes/[recipe-file-name].yml
```

## Exec a recipe using command line
```bash
$ bin/console recipes:exec [recipeName] [on|off|toggle]
```

## Exec a recipe using the front
Just click on the picture to make a toggle execution.
Your http server should target the web/ subfolder

## Providers
A provider allows to make actions. Providers parameters are read from app/config/config.yml.
Each provider should be configuread like this:
```
    my_provider_name:
        provider: [api|binary|freebox|logger|milight|sleep]
        ...
```

Each provider has dedicated parameters, see below.
#### Api
Allows to make network calls.
Parameters: base_url the base url to make the calls
Sample commands: api:endPoint:a-endpoint
### Binary
Allows to call scripts (using php shell_exec command)
Parameters: binary , the full path to binary
Sample commands: binary:command:arg ; binary:echo:arg
#### Freebox
Allows to control freebox using the hd1.freebox.fr api
Parameters: remote_code , the remote code.
Sample commands: freebox: key:home ; freebox: key:1 ; freebox: key:power
#### Logger
Simply write into logs (in var/logs folder) using monolog.
Parameters: log_file the target log file
Sample commands: logger:info:go-to-on
#### Milight
Allows to control milight wifi controller, thanks to [yasharrashedi/LimitlessLED](https://github.com/yasharrashedi/LimitlessLED).
Parameters: host, the milight host ip address.
Sample commands: milight:rgbwAllOn ; milight:rgbwAllOff
#### Sleep
Make a pause between actions
Sample commands: sleep:sleep:5
