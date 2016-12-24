app.service('$voice', function($window, $http){
    var config = null;
    var self = this;

    var methods = {
        listening: false,
        getManager: function(){
            return voiceManager;
        },
        isEnabled: function(){
            return self.getManager().disabled == false;
        },
        setup: function(recipesConfig){

            var manager = self.getManager();
            if (typeof recipesConfig.voices != 'undefined' && recipesConfig.voices){
                config = recipesConfig.voices;
            } else {
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
                recognitionClass = window.webkitSpeechRecognition || window.speechRecognition || window.mozSpeechRecognition || window.webkitSpeechRecognition || SpeechRecognition;
                if (!recognitionClass) {
                    self.disabled = true;
                    return false;
                }
                manager.listener = new recognitionClass();
                manager.listener.lang = apiRecipeConfig.voices.locale;
                manager.listener.continuous = true;
                manager.listener.interimResults = true;

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
            var customResults = new Array;
            var interim_transcript = '';
            for (var i = 0; i < event.results.length; ++i) {
                var result = event.results[i];
                customResults.push(result[0].transcript);
            }
            self.onListened(customResults);
        },
        onListened: function(arr){
            var manager = self.getManager();
            manager.listener.stop();
            if (typeof config.keywords == 'undefined') {
                return false;
            }

            for (var i = 0; i < arr.length; i++) {
                var txt = arr[i];
                txt = txt.toLowerCase();

                for (var i=0; i < config.keywords.length; i++) {
                    var keyword = config.keywords[i].toLowerCase();
                    var trueText = txt.replace(keyword, '');
                    if (trueText != txt) {
                        return self.execVoice(trueText);
                    }
                }
            }
            return false;
        },
        execVoice: function(txt) {
            var manager = self.getManager();
            $http.get(hostApi+'/voice/deduce?text='+encodeURI(txt)).then(function(r){
                if (r.data && r.data.recipe && r.data.voiceMessage) {
                    self.getManager().say(r.data.voiceMessage);
                }
                manager.listener.start();
            });
            return true;
        }
    };

    var self = methods;
    return methods;
});
