
$(document).ready(function() {

	const $body = $('body');
	const is_modal = $body.hasClass('modal') || $body.hasClass('pw-iframe');

	if (!is_modal) return;

	const $save_button = $('#submit_save');
	if (!$save_button.length) return;

	const $saveAndClose = $('<button type="submit" name="submit_save" value="Save" class="ui-button" id="save-and-close">Save + Close</button>');
	$save_button.parent().append($saveAndClose);

	$saveAndClose.on('click', function() {
		sessionStorage.setItem('modialog_close_after_save', '1');
	});

	if (sessionStorage.getItem('modialog_close_after_save')) {
		sessionStorage.removeItem('modialog_close_after_save');
		const hasErrors = $('.NoticeError, .ui-state-error').length > 0;
		if (!hasErrors && window.parent && window.parent.modialog) {
			window.parent.modialog.close();
		}
	}

});
