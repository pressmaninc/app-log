jQuery( document ).ready( function () {
	jQuery( '.path_to_aplg_logdir_class_wrap .delete_btn' ).on( 'click', function () {
		if ( ! confirm( aplg_dashboard_obj.delete_confirm_message ) ) {
			return false;
		}
	});
});