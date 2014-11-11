jQuery(function($) {
	var called = 0;
	$('#wpcontent').ajaxStop(function() {
		if ( 0 == called ) {
			$('[value="uploaded"]').attr( 'selected', true ).parent().trigger('change');
			called = 1;
		}
	});
	var oldPost = wp.media.view.MediaFrame.Post;
	wp.media.view.MediaFrame.Post = oldPost.extend({
		initialize: function() {
			oldPost.prototype.initialize.apply( this, arguments );
			this.states.get('insert').get('library').props.set('uploadedTo', wp.media.view.settings.post.id);
		}
	});
});