class AudioService {
    constructor() {
        this._sounds = {};
        this._disabled = true;
    }

    play(sound, options = {}) {
        const loadedSound = this.getSound(sound, options);
        if (!loadedSound) {
            return;
        }

        loadedSound.currentTime = 0;
        loadedSound.play();
    }

    stop(sound) {
        const loadedSound = this.getSound(sound);
        if (!loadedSound) {
            return;
        }

        loadedSound.pause();
        loadedSound.currentTime = 0;
    }

    getSound(sound) {
        if (this.disabled) {
            return;
        }

        if (!this.sounds[sound]) {
            const filename = AudioService.soundsList[sound];
            if (!filename) {
                return null;
            }

            this.sounds[sound] = new Audio(filename);
        }

        return this.sounds[sound];
    }

    get disabled() {
        return this._disabled;
    }

    get sounds() {
        return this._sounds;
    }

    static get soundsList() {
        return {
            brush: "/sounds/brush.mp3",
            click: "/sounds/click.mp3",
            paintBrush: "/sounds/paintbrush.mp3",
            palette: "/sounds/palette.mp3",
            gong: "/sounds/gong.mp3",
            tick: "/sounds/tick.mp3",
            tock: "/sounds/tock.mp3",
        };
    }
}

AudioService.$inject = [];

export default AudioService;
