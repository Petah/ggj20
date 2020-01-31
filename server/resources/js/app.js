import './bootstrap';

import Canvas from './components/canvas/canvas';
import Game from './components/game/game';
import Palette from './components/palette/palette';

import RootController from './controllers/root';
import LevelSelect from './components/level-select/level-select';

try {
    // Init Angular App
    const app = angular
        .module('GGJ2020', [])
        .component('gGame', Game)
        .component('gCanvas', Canvas)
        .component('gPalette', Palette)
        .component('gLevelSelect', LevelSelect)
        .controller('gRootController', RootController);

    app.config([
        '$httpProvider',
        '$locationProvider',
        function($httpProvider, $locationProvider) {
            $locationProvider.html5Mode({
                enabled: true,
                requireBase: false,
                rewriteLinks: false,
            });

            $httpProvider.defaults.xsrfHeaderName = 'X-CSRF-TOKEN';
            $httpProvider.defaults.xsrfCookieName = 'XSRF-TOKEN';

            $httpProvider.defaults.transformRequest = function(data) {
                if (data === undefined) {
                    return data;
                }

                return $.param(data);
            };

            $httpProvider.defaults.headers.post['Content-Type'] =
                'application/x-www-form-urlencoded; charset=UTF-8';
        },
    ]);

    angular.element(function() {
        angular.bootstrap(document, ['GGJ2020']);
    });

    window.app = app;
} catch (e) {
    console.error(e.message);
}
