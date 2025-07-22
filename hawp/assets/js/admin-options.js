// Define the functions in the global scope immediately
window.hawpSelectImage = function(fieldId) {
    var frame = wp.media({
        title: 'Select Image',
        button: {
            text: 'Use this image'
        },
        multiple: false
    });

    frame.on('select', function() {
        var attachment = frame.state().get('selection').first().toJSON();
        jQuery('#' + fieldId).val(attachment.id);
        jQuery('#' + fieldId + '_preview').html('<img src="' + attachment.sizes.thumbnail.url + '" alt="" />');
        
        // Show the remove button if it doesn't exist
        var controls = jQuery('#' + fieldId).closest('.hawp-image-controls');
        if (controls.find('.button-link-delete').length === 0) {
            controls.append('<input type="button" class="button button-link-delete" value="Remove Image" onclick="hawpRemoveImage(\'' + fieldId + '\')" />');
        }
    });

    frame.open();
};

// Define the remove image function in the global scope
window.hawpRemoveImage = function(fieldId) {
    if (confirm('Are you sure you want to remove this image?')) {
        jQuery('#' + fieldId).val('');
        jQuery('#' + fieldId + '_preview').empty();
        
        // Hide the remove button
        var controls = jQuery('#' + fieldId).closest('.hawp-image-controls');
        controls.find('.button-link-delete').remove();
    }
}; 
