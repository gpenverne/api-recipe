shortcutManager = {
    createShortcut: function(title, base64icon, dataUrl) {
        window.plugins.Shortcut.CreateShortcut({
        	text: title,
            icon: base64icon,
        	extraSubject: dataUrl
        });
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
