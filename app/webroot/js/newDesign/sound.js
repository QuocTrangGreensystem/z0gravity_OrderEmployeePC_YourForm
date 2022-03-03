(function ($) {
  'use strict';

  // Fix up prefixing
  var AudioContext = window.AudioContext || window.webkitAudioContext;
  if (AudioContext)
  {
    var context = new AudioContext();

    var hoverSounds = {};
    var hoverSound = {
      init: function (src)
      {
        var inst = Object.create(hoverSound);
        inst.request = new XMLHttpRequest();
        inst.buffer = null;
        inst.request.open('GET', src, true);
        inst.request.responseType = 'arraybuffer';
        inst.decodedListeners = [];

        // Decode asynchronously
        var onError = function (error) {
          console.log(error);
        };

        inst.request.onload = function () {
          context.decodeAudioData(inst.request.response, function (buffer) {
            inst.buffer = buffer;
            for (var i = 0, length = inst.decodedListeners.length; i < length; i += 1)
            {
              var curr = inst.decodedListeners[i];
              curr();
            }
          }, onError);
        };

        inst.request.send();

        hoverSounds[src] = inst;
        return hoverSounds[src];
      },

      addDecodedListener: function (func) {
        this.decodedListeners.push(func);
      }
    };

    $('[data-hover-sound-src]').each(function (index, item) {
      var src = $(item).attr('data-hover-sound-src');
      var sound = hoverSounds[src] || hoverSound.init(src);
      sound.addDecodedListener(function () {
        item.addEventListener('mouseenter', function (event) {
          var source = context.createBufferSource(); // creates a sound source
          source.buffer = sound.buffer; // tell the source which sound to play
          source.connect(context.destination); // connect the source to the context's destination (the speakers)
          source.start(0); // play the source now
          // note: on older systems, may have to use deprecated noteOn(time);
        });
      });
    });
  }
})(jQuery);
