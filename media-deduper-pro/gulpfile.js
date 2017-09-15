/* eslint-env node */

var gulp  = require( 'gulp' ),
	watch   = require( 'gulp-watch' ),
	phpunit = require( 'gulp-phpunit' ),
	_       = require( 'lodash' ),

	/**
	 * Run the plugin's PHPUnit tests, no more than once every 300ms.
	 */
	runPhpUnit = _.debounce( function() {
		gulp.src( '' )
			.pipe( phpunit().on( 'error', function( e ) {
				console.log( e );
			}) )
	}, 300 );

// 'phpunit' task: run PHPUnit tests once.
gulp.task( 'phpunit', runPhpUnit );

// Default task: watch for changes to PHP files and run PHPUnit tests whenever a
// file is changed.
gulp.task( 'default', function() {
	return watch( '**/*.php', runPhpUnit );
});
