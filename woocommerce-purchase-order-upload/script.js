//
//jQuery(document).ready(function(){
//    setTimeout(function(){
//       
//    },3000);
// 
//
//   
//});
//jQuery('body').on('init_add_payment_method', function(){alert('payment method changed')});
//jQuery('#payment').on('DOMSubtreeModified', function(){
//   console.log('payment changed');
//   setTimeout(function(){
//        initializeUpload();
//    },3000);
//});


function initializeUpload() {

//    if(typeof WCPO_Upload != 'undefineds') return;
        
    var WCPO_Upload = {
        init:function () {
            
             jQuery('#wcpo-uploader').show();
            window.wcpoUploadCount = typeof(window.wcpoUploadCount) == 'undefined' ? 0 : window.wcpoUploadCount;
            this.maxFiles = 1;

            jQuery('#wcpo-upload-imagelist').on('click', 'a.action-delete', this.removeUploads);
           jQuery('#wcpo-upload-loader').hide();
            this.attach();
            this.hideUploader();
             wcpoinit = true;
        },
        attach:function () {
            
            // wordpress plupload if not found
            if (typeof(plupload) === 'undefined') {
               
                return;
            }

//            if (wcpo_upload.upload_enabled !== '1') {
//                console.log('not enabled');
//                return
//            }
            
            var uploader = new plupload.Uploader(wcpo_upload.plupload);

            jQuery('a#wcpo-uploader').on('click', function (e) {
                e.preventDefault();
                 console.log('clicked!');
                uploader.start();               
            });

            //initilize  wp plupload
            uploader.init();

            uploader.bind('FilesAdded', function (up, files) {
               
                jQuery.each(files, function (i, file) {
                    console.log(file);
                    jQuery('#wcpo-upload-imagelist').append(
                        '<div id="' + file.id + '">' +
                            file.name + ' (' + plupload.formatSize(file.size) + ') <b></b>' +
                            '</div>');
                });
                
                up.refresh(); // Reposition Flash/Silverlight
                uploader.start();
            });

            uploader.bind('UploadProgress', function (up, file) {
                jQuery('#' + file.id + " b").html(file.percent + "%");
            });

            // On erro occur
            uploader.bind('Error', function (up, err) {
                jQuery('#wcpo-upload-imagelist').append("<div>Error: " + err.code +
                    ", Message: " + err.message +
                    (err.file ? ", File: " + err.file.name : "") +
                    "</div>"
                );

                up.refresh(); // Reposition Flash/Silverlight
            });

            uploader.bind('FileUploaded', function (up, file, response) {
                var result = jQuery.parseJSON(response.response);
                jQuery('#' + file.id).remove();
              
                if (result.success) {
                    window.wcpoUploadCount += 1;
                    jQuery('#wcpo-upload-imagelist ul').append(result.html);
                    jQuery('input[name= "purchase_order_doc_path"]').val(result.url);
                    WCPO_Upload.hideUploader();
                }
            });


        },

        hideUploader:function () {

            if (WCPO_Upload.maxFiles !== 0 && window.wcpoUploadCount >= WCPO_Upload.maxFiles) {
                jQuery('#wcpo-uploader').hide();
            }
        },

        removeUploads:function (e) {
            e.preventDefault();
jQuery('input[name= "purchase_order_doc_path"]').val('');
            if (confirm(wcpo_upload.confirmMsg)) {

                var el = jQuery(this),
                    data = {
                        'attach_id':el.data('upload_id'),
                        'nonce':wcpo_upload.remove,
                        'action':'wcpo_delete'
                    };

                jQuery.post(wcpo_upload.ajaxurl, data, function () {
                    el.parent().remove();

                    window.wcpoUploadCount -= 1;
                    if (WCPO_Upload.maxFiles !== 0 && window.wcpoUploadCount < WCPO_Upload.maxFiles) {
                        jQuery('#wcpo-uploader').show();
                    }
                });
            }
        }

    };

//if(wcpoinit !=true)
    WCPO_Upload.init();

}