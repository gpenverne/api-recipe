voiceManager = {
    enabled: true,
    listening: false,
    listen: function(callback){
        if (this.listening) {
            return;
        }
        this.listening = true;
        return ;
        window.continuoussr.startRecognize(callback, function(err){ alert(err); }, 5, 'fr-FR');
    },
    say: function(text, locale){
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
   },
   trigged: function(told, expected) {
       return told == expected;
   }
};
