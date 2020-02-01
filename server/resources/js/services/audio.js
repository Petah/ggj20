const AudioService = function() {
    return class AudioService {
        constructor() {
            this._sounds = {};
        }

        play(sound, playbackRate = 1.0, loop = false, force = false) {
            const loadedSound = this.getSound(sound);
            if (!loadedSound) {
                return;
            }

            if (
                !force
                && (loadedSound.currentTime > 0 || loadedSound.ended)
            ) {
                return;
            }

            loadedSound.playbackRate = playbackRate;
            loadedSound.loop = loop;
            loadedSound.play();
        }

        changePlaybackRate(sound, playbackRate = 1.0) {
            const loadedSound = this.getSound(sound);
            if (!loadedSound) {
                return;
            }

            if (loadedSound.playbackRate === playbackRate) {
                return;
            }

            loadedSound.playbackRate = playbackRate;
            loadedSound.play();
        }

        stop(sound, loop = false, force = false) {
            const loadedSound = this.getSound(sound);
            if (!loadedSound) {
                return;
            }

            loadedSound.pause();
            loadedSound.currentTime = 0;
        }

        getSound(sound) {
            if (!this.sounds[sound]) {
                const filename = AudioService.soundsList[sound];
                if (!filename) {
                    return null;
                }

                this.sounds[sound] = new Audio(filename);
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
