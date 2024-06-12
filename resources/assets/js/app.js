/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
*/
// Axios
// require('./bootstrap');

// Bootstrap
require('bootstrap/dist/js/bootstrap.bundle');
require('bootstrap');
// Jquery
global.$ = global.jQuery = global.jquery = require('jquery');
require('jquery.easing');
// Select 2
require('select2/dist/js/select2');
// DataTables
window.datatables = require('datatables.net-bs4');
window.dt = require('datatables.net');
require('datatables.net-responsive');
// Filepond
// window.FilePond = require('filepond/dist/filepond.min');
// Admin
require('./admin');