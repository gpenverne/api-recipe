voiceManager = {
    listening: false,
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
