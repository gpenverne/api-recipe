var apiRecipeConfig = null;
var waitingForWords = false;
var apiRecipeConfigLoaded = false;

var shortcutManager = {
    hadShortcut: false,
    createShortcut: function(recipe) {
        return false;
    },
    hasShortcutCalled: function() {
        return false;
    },
    execExtra: function() {
        return false;
    }
};

var voiceManager = {
    listener: null,
    listening: false,
    setted: false,
    disabled: false,
    say: function(text){
        var msg = new SpeechSynthesisUtterance();
        msg.volume = 1; // 0 to 1
        msg.rate = 1; // 0.1 to 10
        msg.pitch = 2; //0 to 2
        msg.text = text;
        msg.lang = 'fr-FR';
        speechSynthesis.speak(msg);
    }
};

if (typeof hostApi == 'undefined' || null == hostApi) {
    hostApi = 'http://'+window.location.host;
}

var app = angular.module('app', ['ngTouch', 'pr.longpress']).service('currentTag', function(){
    var currentTag = 'all';
    return {
        doNotShowAllTags: false,
        getCurrentTag: function() {
            return currentTag;
        },
        setCurrentTag: function(tag){
            currentTag = tag;
        }
    };
});
