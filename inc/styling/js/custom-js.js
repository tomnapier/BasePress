jQuery(document).ready( function(jQuery) {

	jQuery('#media-items').bind('DOMNodeInserted',function(){
		jQuery('input[value="Insert into Post"]').each(function(){
				jQuery(this).attr('value','Use This Image');
		});
	});
	
	jQuery('.custom_upload_image_button').click(function() {
		formfield = jQuery(this).siblings('.custom_upload_image');
		preview = jQuery(this).siblings('.custom_preview_image');
		tb_show('', 'media-upload.php?type=image&TB_iframe=true');
		window.send_to_editor = function(html) {
			imgurl = jQuery('img',html).attr('src');
			classes = jQuery('img', html).attr('class');
			id = classes.replace(/(.*?)wp-image-/, '');
			formfield.val(id);
			preview.attr('src', imgurl);
			tb_remove();
		}
		return false;
	});

	var _custom_media = true,
		_orig_send_attachment = wp.media.editor.send.attachment;

	jQuery('.custom_upload_media_button').click(function(e){
		var send_attachment_bkp = wp.media.editor.send.attachment,
			button = jQuery(this),
			id = button.attr('id').replace('_button', ''),
			img = button.attr('id').replace('_button', '_img'),
			remove = button.attr('id').replace('_button', '_remove');
		_custom_media = true;
		wp.media.editor.send.attachment = function(props, attachment){
			if ( _custom_media ) {
				jQuery("#" + id).val( attachment.id );
				jQuery("#" + img).css('display', 'block').find('.filename div').html( attachment.filename );
				jQuery("#" + remove).css('display', 'block');
				button.val( 'Choose Another File' );
				console.log( attachment );
			} else {
				return _orig_send_attachment.apply( this, [props, attachment] );
			};
		}

		wp.media.editor.open( button );
		return false;
	});

	jQuery('.remove_custom_upload_media').click(function(e){
		var remove = jQuery(this),
			id = remove.attr('id').replace('_remove', ''),
			img = remove.attr('id').replace('_remove', '_img'),
			button = remove.attr('id').replace('_remove', '_button');

		jQuery("#" + id).val( '' );
		jQuery("#" + img).css('display', 'none').find('.filename div').html( '' );
		jQuery("#" + button).val( 'Add File' );
		remove.css('display', 'none');

		return false;
	});

	jQuery('.custom_clear_image_button').click(function() {
		var defaultImage = jQuery(this).parent().siblings('.custom_default_image').text();
		jQuery(this).parent().siblings('.custom_upload_image').val('');
		jQuery(this).parent().siblings('.custom_preview_image').attr('src', defaultImage);
		return false;
	});
	
	jQuery('.repeatable-add').click(function() {
		field = jQuery(this).closest('td').find('.custom_repeatable li:last').clone(true);
		fieldLocation = jQuery(this).closest('td').find('.custom_repeatable li:last');
		jQuery('input', field).val('').attr('name', function(index, name) {
			return name.replace(/(\d+)/, function(fullMatch, n) {
				return Number(n) + 1;
			});
		})
		field.insertAfter(fieldLocation, jQuery(this).closest('td'))
		return false;
	});
	
	jQuery('.repeatable-remove').click(function(){
		jQuery(this).closest('li').remove();
		return false;
	});
		
	jQuery('.custom_repeatable').sortable({
		opacity: 0.6,
		revert: true,
		cursor: 'move',
		handle: '.sort'
	});

	jQuery(".datepicker").datepicker({ dateFormat: "yy/mm/dd" });

	jQuery( ".jquery-slider" ).each(function(){
		var $self = jQuery(this),
			$id = $self.attr( 'id' ).replace( '-slider', '' ),
			$min = $self.attr( 'data-min' ),
			$max = $self.attr( 'data-max' ),
			$step = $self.attr( 'data-step' ),
			$value = jQuery( '#' + $id ).val();


		jQuery(this).slider({
			value: $value,
			min: $min,
			max: $max,
			step: $step,
			slide: function( event, ui ) {
				jQuery( '#' + $id ).val( ui.value );
			}
		});
	});
});