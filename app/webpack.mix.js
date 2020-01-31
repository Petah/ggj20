const mix = require("laravel-mix");

mix
    .js("src/js/app.js", "public/app.js")
    .sass("src/scss/app.scss", "public/app.css");
