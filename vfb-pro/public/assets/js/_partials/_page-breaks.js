jQuery(document).ready(function($) {
	if ( $.fn.vfbSteps ) {
		var btnPrev      = vfbp_pageBreak_L10n.btnPrev,
			btnNext      = vfbp_pageBreak_L10n.btnNext,
			titleDisplay = vfbp_pageBreak_L10n.titleDisplay,
			titleClick   = vfbp_pageBreak_L10n.titleClick,
			numDisplay   = vfbp_pageBreak_L10n.numDisplay;

		$( '.vfbp-form' ).vfbSteps({
			titleDisplay: titleDisplay,
			titleClick: titleClick,
			numDisplay: numDisplay,
			validate: true,
			block: true,
			backLabel: btnPrev,
			nextLabel: btnNext
		});
	}
});