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
    }
];
module.exports.metadata = 
// TOP OF METADATA
{
    "cordova-plugin-device": "1.1.3",
    "com.lampa.startapp": "0.1.4"
}
// BOTTOM OF METADATA
});