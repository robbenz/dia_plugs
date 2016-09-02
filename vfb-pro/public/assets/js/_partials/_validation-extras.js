jQuery(document).ready(function($) {
	var msgMinWords   = 'This value is too short. It should have %s words or more.',
		msgMaxWords   = 'This value is too long. It should have %s words or fewer.',
		msgWordsRange = 'This value length is invalid. It should be between %s and %s words long.';

	// If custom validation messages
	if ( window.vfbp_validation_custom ) {
		var messages = vfbp_validation_custom.vfbp_messages;

		msgMinWords   = messages.minwords;
		msgMaxWords   = messages.maxwords;
		msgWordsRange = messages.words;
	}

	// minwords, maxwords, words extra validators
	var countWords = function (string) {
	  return string
	      .replace( /(^\s*)|(\s*$)/gi, "" )
	      .replace( /[ ]{2,}/gi, " " )
	      .replace( /\n /, "\n" )
	      .split(' ').length;
	};

	window.ParsleyValidator.addValidator(
		'minwords',
		function (value, nbWords) {
			return countWords(value) >= nbWords;
		}, 32)
		.addMessage( 'en', 'minwords', msgMinWords );

	window.ParsleyValidator.addValidator(
		'maxwords',
		function (value, nbWords) {
			return countWords(value) <= nbWords;
		}, 32)
		.addMessage( 'en', 'maxwords', msgMaxWords );

	window.ParsleyValidator.addValidator(
		'words',
		function (value, arrayRange) {
			var length = countWords(value);
			return length >= arrayRange[0] && length <= arrayRange[1];
		}, 32)
		.addMessage( 'en', 'words', msgWordsRange );

	// gt, gte, lt, lte extra validators
	var parseRequirement = function (requirement) {
	  if ( isNaN( +requirement ) ) {
	    return parseFloat( $( requirement ).val() );
	  }
	  else {
	    return +requirement;
	  }
	};

	// gt, gte, lt, lte extra validators
	window.ParsleyConfig = window.ParsleyConfig || {};
	window.ParsleyConfig.validators = window.ParsleyConfig.validators || {};

	// Greater than validator
	window.ParsleyConfig.validators.gt = {
	  fn: function (value, requirement) {
	    return parseFloat(value) > parseRequirement(requirement);
	  },
	  priority: 32
	};

	// Greater than or equal to validator
	window.ParsleyConfig.validators.gte = {
	  fn: function (value, requirement) {
	    return parseFloat(value) >= parseRequirement(requirement);
	  },
	  priority: 32
	};

	// Less than validator
	window.ParsleyConfig.validators.lt = {
	  fn: function (value, requirement) {
	    return parseFloat(value) < parseRequirement(requirement);
	  },
	  priority: 32
	};

	// Less than or equal to validator
	window.ParsleyConfig.validators.lte = {
	  fn: function (value, requirement) {
	    return parseFloat(value) <= parseRequirement(requirement);
	  },
	  priority: 32
	};
});