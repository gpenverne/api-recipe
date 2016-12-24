voiceManager.say =  function(text, locale){
    try {
        TTS.speak({
           text: text,
           locale: locale,
           rate: 1
       }, function () {
           // Do Something after success
       }, function (reason) {
           // Handle the error case
       });
    } catch(e){
        console.log('cant tell now');
    }
};

var customAndroidSpeechRecognition = {
    lang: 'en-US',
    continuous: true,
    interimResults: true,
    getOptions: function(){
        return {
            'language': this.lang,
            'matches': 1,
            'showPopup': false
        };
    },
    start: function(){
        return window.continuoussr.startRecognize(this.handleOnResult, this.onerrir, this.getOptions().matches, this.getOptions().language);
    },
    stop: function(){

    },
    onerror: function(e) {

    },
    onresult: function(e) {
        alert(e);
    },
    handleOnResult: function(arrayItems){
        var customEvent = {
            resultIndex: 0,
            results: new Array,
        }
        var txt = arrayItems[0].toLowerCase();
        var config = voicesConfig.voices;

        for (var i=0; i < config.keywords.length; i++) {
            var keyword = config.keywords[i].toLowerCase();
            var trueText = txt.replace(keyword, '');
            if (trueText != txt) {
                var url = hostApi+'/voice/deduce?text='+encodeURI(trueText);
                $.get(url);
            }
        }
            return false;
    }
};

window.androidSpeechRecognition = function(){
    return customAndroidSpeechRecognition;
}
