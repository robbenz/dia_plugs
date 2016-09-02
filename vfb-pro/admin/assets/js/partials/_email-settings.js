jQuery(document).ready(function($) {
	$( '#email-to' ).tagsInput({
	    width: '35em',
	    height: 'auto',
	    defaultText: 'add an email',
	    pattern: /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/
	});
});