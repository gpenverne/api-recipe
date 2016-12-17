var globTitle;
var globBase64icon;
var globDataUrl;

shortcutManager = {
    hadShortcut: false,
    createShortcut: function(recipe) {
        globTitle = recipe.title;
        globBase64icon = recipe.icon;
        globDataUrl = {url: recipe.url, app: recipe.androidApp};

        navigator.notification.confirm('Create a shortcut on your launcher?', function(btnIndex){
            if (1 === btnIndex) {
                var args = {
                	text: globTitle,
                    icon: globBase64icon,
                	extraSubject: JSON.stringify(globDataUrl)
                };
                window.plugins.Shortcut.CreateShortcut(args);
            }
        }, 'Confirmation', ['Yes', 'No']);

    },
    execExtra: function() {
        try {
            window.plugins.webintent.getExtra(window.plugins.webintent.EXTRA_SUBJECT,
                function(data) {
                    data = JSON.parse(data);
                    if (data.app) {
                        handleAndroidAppLaunch(data.app);
                    }
                    $.get(window.localStorage.getItem("host") + data.url, function(){
                        navigator.app.exitApp();
                    });

                }, function() {
                    //no data
                }
            );
        } catch(e){
        }
    },
    clearExtra: function() {
        	window.plugins.webintent.removeExtra(window.plugins.webintent.EXTRA_SUBJECT,
                function() {
                    // Extra was removed
                }, function() {
                    // Something really bad happened.
                }
            );
    }
};
