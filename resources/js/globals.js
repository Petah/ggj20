// jQuery and Angular as globals, before the Bootstrap jQuery plugins load.
import $ from 'jquery';
import angular from 'angular';

window.$ = window.jQuery = $;
window.angular = angular;
