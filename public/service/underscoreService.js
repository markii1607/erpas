define([
	'app'
], function (app) {
	app.service('_', [
		'$window',
		function ($window) {
	        return $window._; // assumes underscore has already been loaded on the page
		}
	]);
});