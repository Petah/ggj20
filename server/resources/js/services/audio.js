import {Howl, Howler} from 'howler';

const AudioService = function() {
    return class AudioService {
        constructor() {
            this._sounds = {};
        }

        play(sound, options = {}) {
            const loadedSound = this.getSound(sound, options);
            if (!loadedSound) {
                return;
            }

            loadedSound.seek(0);
            loadedSound.play();
        }

        stop(sound) {
            const loadedSound = this.getSound(sound);
            if (!loadedSound) {
                return;
            }

            loadedSound.stop();
            loadedSound.seek(0);
        }

        getSound(sound, options = {}) {
            if (!this.sounds[sound]) {
                const filename = AudioService.soundsList[sound];
                if (!filename) {
                    return null;
                }

                this.sounds[sound] = new Howl({
                    src: [filename],
                    loop: false,
                    volume: 0.8,
                    ...options,
                });
            }

            return this.sounds[sound];
        }

        get sounds() {
            return this._sounds;
        }

        static get soundsList() {
            return {
                click: "/sounds/click.mp3",
                paintBrush: "/sounds/paintbrush.mp3",
                palette: "/sounds/palette.mp3",
                tickTock: "/sounds/tick-tock.mp3",
            };
        }
    };
};

AudioService.$inject = [];

export default AudioService;
