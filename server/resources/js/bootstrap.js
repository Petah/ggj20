try {
    // Init libs
    window.$ = window.jQuery = require("jquery");
    window.angular = require("angular");
} catch (e) {
    console.error(e.message);
}
