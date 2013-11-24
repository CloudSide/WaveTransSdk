/**
 * WaveTrans for js (Web Audio)
 * @author Bruce Chen <662005@qq.com> @一个开发者
 */

(function() {
  var baseFrequency, beepLength, char, characters, context, freq, freqCodes, frequencies, i, semitone, _i, _len;

  window.AudioContext || (window.AudioContext = window.webkitAudioContext || window.mozAudioContext || window.msAudioContext || window.oAudioContext);

  semitone = 1.05946311;

  baseFrequency = 1760;

  beepLength = 87.2;

  characters = '0123456789abcdefghijklmnopqrstuv';

  freqCodes = {};

  frequencies = [];

  for (i = _i = 0, _len = characters.length; _i < _len; i = ++_i) {
  
    char = characters[i];
    freq = +(baseFrequency * Math.pow(semitone, i)).toFixed(3);
    freqCodes[char] = freq;
    frequencies[i] = freq;
  }

  context = new AudioContext();

  window.chirp = function(message, ecc) {
    var chirp, front_door, gainNode, now, oscillator, _j, _len1;
    front_door = 'hj';
    chirp = front_door + message + ecc;
    oscillator = context.createOscillator();
    oscillator.type = 0;
    gainNode = context.createGainNode();
    gainNode.gain.value = 0.6;
    oscillator.connect(gainNode);
    gainNode.connect(context.destination);
    now = context.currentTime;
    for (i = _j = 0, _len1 = chirp.length; _j < _len1; i = ++_j) {
      char = chirp[i];
      oscillator.frequency.setValueAtTime(freqCodes[char], now + (beepLength / 1000 * i));
    }
    oscillator.start(now);
    return oscillator.stop(now + (beepLength / 1000 * (chirp.length + 1)));
  };
  
}).call(this);
