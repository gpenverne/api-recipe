var voicesConfig;

app.service('$voice', function($window, $http){
    var config = null;
    var self = this;
    var $voice = this;

    var methods = {
        working: false,
        listening: false,
        getManager: function(){
            return voiceManager;
        },
        isEnabled: function(){
            return self.getManager().disabled == false;
        },
        setup: function(recipesConfig, force){
            voicesConfig = recipesConfig;

            var manager = self.getManager();
            if (typeof recipesConfig.voices != 'undefined' && recipesConfig.voices){
                config = recipesConfig.voices;
            } else {
                console.log('no settings!');
                self.disabled = true;
                return false;
            }

            if (manager.listening || manager.setted || self.listening) {
                self.disabled = false;
                return true;
            }
            manager.setted = true;
            self.disabled = false;
            try {
                recognitionClass = window.webkitSpeechRecognition || window.speechRecognition || window.mozSpeechRecognition || window.webkitSpeechRecognition || window.androidSpeechRecognition || SpeechRecognition;
                if (!recognitionClass) {
                    self.disabled = true;
                    return false;
                }
                manager.listener = new recognitionClass();
                manager.listener.lang = apiRecipeConfig.voices.locale;
                manager.listener.continuous = true;
                manager.listener.maxAlternatives = 1;
                manager.listener.onend = function(){
                    var manager = self.getManager();
                    manager.listening = false;
                    self.listening = false;
                };
                manager.listener.onresult = self.onResult;
                manager.listener.start();
                manager.listening = true;
                self.listening = true;
                return true;
            } catch (e) {
                manager.listening = false;
                self.listening = false;
                manager.listener = null;
                self.disabled = true;
                return false;
            }
        },
        onResult: function(event){
            var interim_transcript = '';
            if (typeof  event.resultIndex == 'undefined') {
                 event.resultIndex = event.results.length - 1;
            }
            for (var i = event.resultIndex; i < event.results.length; ++i) {
                var result = event.results[i];
                console(result[0].transcript);
                self.onListened(result[0].transcript);
                return true;
            }

            return true;

        },
        toggle: function(){
            var manager = self.getManager();
            try {
                if (manager.listening) {
                    if (manager.listener) {
                        manager.listener.stop();
                    }
                    manager.listening = false;
                } else {
                    if (manager.listener) {
                        manager.listener.start();
                    }
                    manager.listening = true;
                }
            } catch(e){}
        },
        onListened: function(txt){

            if (typeof config.keywords == 'undefined') {
                return false;
            }

            txt = txt.toLowerCase();
            for (var i=0; i < config.keywords.length; i++) {
                var keyword = config.keywords[i].toLowerCase();
                var trueText = txt.replace(keyword, '');
                if (trueText != txt) {

                    return self.execVoice(trueText);
                }
            }

        },
        execVoice: function(txt) {

            var manager = self.getManager();
            $http.get(hostApi+'/voice/deduce?text='+encodeURI(txt)).then(function(r){
                if (r.data && r.data.recipe && r.data.voiceMessage) {
                    manager.say(r.data.voiceMessage);
                }

            });
            return true;
        }
    };

    var self = methods;
    return methods;
});
