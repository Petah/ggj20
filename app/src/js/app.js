import "./bootstrap";

import Game from "./components/game/game";

import RootController from "./controllers/root";

try {
    // Init Angular App
    const app = angular
        .module("GGJ2020", [])
        .component("gGame", Game)
        .controller("gRootController", RootController);

    app.config([
        "$httpProvider",
        "$locationProvider",
        function($httpProvider, $locationProvider) {
            $locationProvider.html5Mode({
                enabled: true,
                requireBase: false,
                rewriteLinks: false,
            });

            $httpProvider.defaults.xsrfHeaderName = "X-CSRF-TOKEN";
            $httpProvider.defaults.xsrfCookieName = "XSRF-TOKEN";

            $httpProvider.defaults.transformRequest = function(data) {
                if (data === undefined) {
                    return data;
                }

                return $.param(data);
            };

            $httpProvider.defaults.headers.post["Content-Type"] =
                "application/x-www-form-urlencoded; charset=UTF-8";
        },
    ]);

    angular.element(function() {
        angular.bootstrap(document, ["GGJ2020"]);
    });

    window.app = app;
} catch (e) {
    console.error(e.message);
}
