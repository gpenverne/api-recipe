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
        alert('attempt to start');
        alert(this.onresult);
        return window.plugins.speechRecognition.startListening(this.handleOnResult, this.onerror, this.getOptions());
    },
    stop: function(){},
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

        for (i =0; i < arrayItems.length; i++) {
            item = arrayItems[i];
            itemArray = new Array;
            itemArray.push({
                transcript: item
            });

            customEvent.results.push(itemArray);
        }

        this.onresult(customEvent);
    }
};

window.androidSpeechRecognition = function(){
    return customAndroidSpeechRecognition;
}
