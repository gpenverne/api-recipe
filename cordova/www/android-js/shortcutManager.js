var globTitle;
var globBase64icon;
var globDataUrl

shortcutManager = {
    createShortcut: function(title, base64icon, dataUrl) {
        globTitle = title;
        globBase64icon = base64icon;
        globDataUrl = dataUrl;

        navigator.notification.confirm('Create a shortcut on your launcher?', function(btnIndex){
            if (1 === btnIndex) {
                window.plugins.Shortcut.CreateShortcut({
                	text: globTitle,
                    icon: globBase64icon,
                	extraSubject: globDataUrl
                });
            }
        }, 'Confirmation', ['Yes', 'No']);

    },
    hasShortcutCalled: function() {
        window.plugins.webintent.getExtra(window.plugins.webintent.EXTRA_SUBJECT,
            function(data) {
                $.get('http://'+window.localStorage.getItem("host") + data, function(){
                    navigator.app.exitApp();
                });
            }, function() {
                //no data
            }
        );
    }
};


document.addEventListener("deviceready", shortcutManager.hasShortcutCalled(), false);
