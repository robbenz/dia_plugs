jQuery(document).ready(function($) {
	if ( $.fn.fileinput ) {
		$( '.vfb-file-input' ).fileinput({
			showUpload : false,
			layoutTemplates: {
				main1: '{preview}\n' +
			        '<div class="vfb-input-group {class}">\n' +
			        '   {caption}\n' +
			        '   <div class="vfb-input-group-btn">\n' +
			        '       {remove}\n' +
			        '       {upload}\n' +
			        '       {browse}\n' +
			        '   </div>\n' +
			        '</div>',
			    main2: '{preview}\n{remove}\n{upload}\n{browse}\n',
			    preview: '<div class="file-preview {class}">\n' +
			        '   <div class="vfb-close fileinput-remove text-right">×</div>\n' +
			        '   <div class="file-preview-thumbnails"></div>\n' +
			        '   <div class="vfb-clearfix"></div>' +
			        '   <div class="file-preview-status text-center text-success"></div>\n' +
			        '   <div class="kv-fileinput-error"></div>\n' +
			        '</div>',
			    icon: '',
			    caption: '<div tabindex="-1" class="vfb-form-control file-caption {class}">\n' +
			        '   <div class="file-caption-name"></div>\n' +
			        '</div>',
			    modal: '<div id="{id}" class="modal fade">\n' +
			        '  <div class="modal-dialog modal-lg">\n' +
			        '    <div class="modal-content">\n' +
			        '      <div class="modal-header">\n' +
			        '        <button type="button" class="vfb-close" data-dismiss="modal" aria-hidden="true">×</button>\n' +
			        '        <h3 class="modal-title">Detailed Preview <small>{title}</small></h3>\n' +
			        '      </div>\n' +
			        '      <div class="modal-body">\n' +
			        '        <textarea class="vfb-form-control" style="font-family:Monaco,Consolas,monospace; height: {height}px;" readonly>{body}</textarea>\n' +
			        '      </div>\n' +
			        '    </div>\n' +
			        '  </div>\n' +
			        '</div>\n'
			}
		});
	}
});