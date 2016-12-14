# Homatisation

The purpose of this repo is to generate a simple front interface to make api calls. This app has been designed for use with the
[broadlink rm-pro](http://amzn.to/2hiTWk5) and the [rm-bridge android app](https://play.google.com/store/apps/details?id=de.fun2code.android.rmbridge)

An android version of the front is available in [cordova/releases folder](https://github.com/gpenverne/homatisation/tree/master/cordova/releases)

## Install
### Installing dependencies
```bash
$ composer install
```
### Installing assets
```bash
$ bower install
```
### WebServer configuration
Using nginx:
```
    location / {
        try_files $uri /index.php$is_args$args;
    }
```

## Recipes
A recipe contains actions.  
Put your recipes files in the recipes folder.  
Check the test-recipe.yml file for sample configuration.  
Put your images in the web/images folder.  

### Test a recipe yml syntax
```bash
$ bin/yaml-lint recipes/[recipe-file-name].yml
```

### Create a recipe using command line
```bash
$ bin/console recipes:create
```

### Exec a recipe using command line
```bash
$ bin/console recipes:exec [recipeName] [on|off|toggle]
```

### Exec a recipe using the front
Just click on the picture to make a toggle execution.  
Your http server should target the web/ subfolder.  

### tags
Each recipe can have one or more tags. These tags will be shown in the slide menu (android app).

## Actions
An action is a string following this syntax: provider_name:provider_method:optional_argument .

### Exec an action using command line
```bash
$ bin/console actions:exec
$ bin/console actions:exec [provider_name:provider_method:optional_argument]
```

## Providers
A provider allows to make actions. Providers parameters are read from app/config/config.yml.  
Each provider should be configuread like this:  
```
    my_provider_name:
        provider: [android|api|binary|confirm|freebox|logger|milight|sleep]
        ...
```

Each provider has dedicated parameters, see below.
#### Android
Allows to launch apps (only in the android app).  
Sample commands: android:openApp:com.google.android.music  
#### Api
Allows to make network calls.  
Parameters: base_url the base url to make the calls  
Sample commands: api:endPoint:an-endpoint  
#### Binary
Allows to call scripts (using php shell_exec command)  
Parameters: binary , the full path to binary  
Sample commands: binary:command:arg ; binary:echo:arg  
#### Confirm
It asks user to confirm the action launc   
Sample commands: confirm:confirm:confirmation message  
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
