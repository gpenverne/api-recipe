# Api Recipe

With Api Recipe, you can exec recipes from command line or from a front interface.  
Each recipe is a set of calls (which are related to "Providers", see below).  

The purpose of this repo is to generate a simple front interface to make api calls.   
This app has been designed for use with the
[broadlink rm-pro](http://amzn.to/2hiTWk5) and the [rm-bridge android app](https://play.google.com/store/apps/details?id=de.fun2code.android.rmbridge)

An android version of the front is available in [cordova/releases folder](https://github.com/gpenverne/api-recipe/tree/master/cordova/releases)

## Install
### Installing dependencies
```bash
$ composer install
```
### Installing assets for front
```bash
$ bower install
```

### WebServer configuration
#### Using nginx:
```
server {
    root [FULL PATH TO API RECIPE]api-recipe/web;

    location / {
        try_files $uri /index.php$is_args$args;
    }

    # for use with php7 fpm for example
    location ~ \.php(/|$) {
        fastcgi_pass unix:/run/php/php7.0-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
    }
}
```
#### Using built-in webserver:  
Use the app/config/config.yml file to customize port and address (default is localhost on port 80).  
To start the built in webserver:
```bash
$ php bin/console api-recipe:server start
```
To restart the built in webserver:
```bash
$ php bin/console api-recipe:server restart
```
To stop the built in webserver:
```bash
$ php bin/console api-recipe:server stop
```
## Recipes
A recipe contains actions.  
Each action is written using this syntax:  
```
   provider:action:state
```

Put your recipes files in the recipes folder.  
Check the test-recipe.yml file for sample configuration.  
Put your images in the web/images folder.  

You can call another recipe using this syntax:
```
  recipe:recipeName:state
```  

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
        provider: [android|api|binary|cecClient|confirm|freebox|logger|milight|sleep]
        ...
```

Each provider has dedicated parameters, see below.
#### Android
Allows to launch apps (only in the android app).  
Sample commands: android:openApp:com.google.android.music  
#### Api
Allows to make network calls.  
Parameters: base_url the base url to make the calls  
Sample commands: api:sync:an-endpoint  
Sample commands: api:async:an-endpoint  
#### Binary
Allows to call scripts (using php shell_exec command)  
Parameters: binary , the full path to binary  
Sample commands: binary:command:arg ; binary:echo:arg  
### CecClient
Send command to [cec-client](http://manpages.ubuntu.com/manpages/trusty/man1/cec-client.1.html)  
Parameters: binary, the full path to cec-client binary
Sample commands: cec-client:command:as ; cec-client:command:standby 0 ; cec-client:command:on 0 ; cec-client:setActiveSource:freebox
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


## Voice
You can use voice recognition (on both html5 interface and android application).  
For example, in a config recipe yml file:
```
voices:
    on:
        triggers:
            # php regexp to match expression
            - '%test(.*?)on%'
        # the voice message return
        message: 'Test on ok'
    off:
        triggers:
            # php regexp to match expression
            - '%test(.*?)off%'
        # the voice message return
        message: 'Test off ok'
    each_time:
        triggers:
            # php regexp to match expression
            - '%test each%'
        # the voice message return
        message: 'Test each ok'
```

## JARVIS
You can link api-recipe with jarvis. Have a look on (https://github.com/gpenverne/api-recipe-jarvis-plugin)[https://github.com/gpenverne/api-recipe-jarvis-plugin]
