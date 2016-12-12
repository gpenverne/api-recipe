cordova.define('cordova/plugin_list', function(require, exports, module) {
module.exports = [
    {
        "file": "plugins/cordova-plugin-device/www/device.js",
        "id": "cordova-plugin-device.device",
        "clobbers": [
            "device"
        ]
    },
    {
        "file": "plugins/com.lampa.startapp/www/startApp.js",
        "id": "com.lampa.startapp.startapp",
        "merges": [
            "startApp"
        ]
    },
    {
        "file": "plugins/com.plugins.shortcut/www/ShortcutPlugin.js",
        "id": "com.plugins.shortcut.ShortcutPlugin",
        "clobbers": [
            "ShortcutPlugin"
        ]
    },
    {
        "file": "plugins/com.virtualartifacts.webintent/www/webintent.js",
        "id": "com.virtualartifacts.webintent.WebIntent",
        "clobbers": [
            "WebIntent"
        ]
    }
];
module.exports.metadata = 
// TOP OF METADATA
{
    "cordova-plugin-device": "1.1.3",
    "com.lampa.startapp": "0.1.4",
    "com.plugins.shortcut": "0.1.1",
    "com.virtualartifacts.webintent": "1.0.0"
}
// BOTTOM OF METADATA
});