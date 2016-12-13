var globTitle;
var globBase64icon;
var globDataUrl;

shortcutManager = {
    hadShortcut: false,
    createShortcut: function(title, base64icon, dataUrl) {
        globTitle = title;
        globBase64icon = base64icon;
        globDataUrl = dataUrl;
        navigator.notification.confirm('Create a shortcut on your launcher?', function(btnIndex){
            if (1 === btnIndex) {
                var args = {
                	text: globTitle,
                    icon: globBase64icon,
                	extraSubject: globDataUrl
                };
                window.plugins.Shortcut.CreateShortcut(args);
            }
        }, 'Confirmation', ['Yes', 'No']);

    },
    hasShortcutCalled: function() {
        shortcutManager.execExtra();
    },
    execExtra: function() {
        try {
            window.plugins.webintent.getExtra(window.plugins.webintent.EXTRA_SUBJECT,
                function(data) {
                    $.get(window.localStorage.getItem("host") + data, function(){
                        navigator.app.exitApp();
                    });
                    shortcutManager.clearExtra();
                }, function() {
                    //no data
                }
            );
        } catch(e){}
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
document.addEventListener("deviceready", onDeviceReady, false);
function onDeviceReady() {
    document.addEventListener("resume", shortcutManager.hasShortcutCalled(), false);
}
