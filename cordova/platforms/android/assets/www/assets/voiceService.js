app.service('$voice', function($window, $http){
    var config = null;
    var self = this;

    var methods = {
        getManager: function(){
            return voiceManager;
        },
        isEnabled: function(){
            return self.getManager().disabled == false;
        },
        setup: function(recipesConfig){
            console.log('Trying to setup voice Manager with settings ' + JSON.stringify(recipesConfig));

            var manager = self.getManager();
            if (typeof recipesConfig.voices != 'undefined' && recipesConfig.voices){
                config = recipesConfig.voices;
            } else {
                console.log('Config not found');
                self.disabled = true;
                return false;
            }

            if (manager.listening || manager.setted) {
                self.disabled = false;
                return true;
            }
            manager.setted = true;
            self.disabled = false;
            try {
                recognitionClass = window.webkitSpeechRecognition || window.speechRecognition || window.mozSpeechRecognition || window.webkitSpeechRecognition || window.androidSpeechRecognition;
                if (!recognitionClass) {
                    self.disabled = true;
                    alert('unable to use voice recognition');
                    return false;
                }
                manager.listener = new recognitionClass();
                manager.listener.lang = apiRecipeConfig.voices.locale;
                manager.listener.continuous = true;
                manager.listener.interimResults = true;

                manager.listener.onresult = this.onResult;

                manager.listener.start();
                manager.listening = true;
                return true;
            } catch (e) {
                manager.listening = false;
                manager.listener = null;
                self.disabled = true;
                return false;
            }
        },
        onResult: function(event){
            var interim_transcript = '';
            for (var i = event.resultIndex; i < event.results.length; ++i) {
                var result = event.results[i];
                if (result.isFinal) {
                    self.onListened(result[0].transcript);
                }
            }
        },
        onListened: function(){
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
            return false;
        },
        execVoice: function(txt) {
            $http.get(hostApi+'/voice/deduce?text='+encodeURI(txt)).then(function(r){
                if (r.data && r.data.recipe && r.data.voiceMessage) {
                    self.getManager().say(r.data.voiceMessage);
                }
            });
            return true;
        }
    };

    var self = methods;
    return methods;
});
