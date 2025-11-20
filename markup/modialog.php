<?php

/**
 * Add a dialog element and script so we can load a page in a modal dialog
 * 
 * We normally use this for editing a page in a modal dialog.
 * 
 * 
 * */

?>

<!-- Add a dialog element and script so we can load a page in a modal dialog -->
<dialog id="modialog" class="millco_dialog">
	<header class="millco_dialog_close_container">
		<button id="modialog_close" class="millco_dialog_close" type="button" autofocus>
			<span class="millco_dialog_close_icon"><?= $mu->icon('close')?></span>
			<span class="millco_dialog_close_text">Close</span>
		</button>
		<span class="md_warning">Remember to save your changes before closing the dialog.</span>
	</header>
	<div id="modialog_content"></div>
</dialog>

<script nonce="<?= $mu->nonce; ?>">
	window.modialog_needs_reload = true; // by default we reload the page when the dialog is closed

	document.addEventListener('DOMContentLoaded', function() {

		// Add event listener for the close button.
		modialog_close.addEventListener("click", () => {
			modialog.close();
		});

		// When we close the dialog we check if we need to reload the page.
		modialog.addEventListener("close", (event) => {

			if (modialog.classList.contains('dialog_embiggen')) {
				modialog.classList.remove('dialog_embiggen');

				if (window.modialog_needs_reload) {
					window.location.reload(true);
				}
			}
		});

		function modialog_iframe(iframe_url) {

			let iframe = document.createElement('iframe');

			iframe.src = iframe_url;
			iframe.style.width = '100%';
			iframe.style.minWidth = '1024px';
			iframe.style.minHeight = '600px';
			iframe.style.height = '100%';
			iframe.style.border = 'none';
			modialog_content.innerHTML = '';
			modialog_content.appendChild(iframe);

			modialog.classList.add('dialog_embiggen');

			modialog.showModal();
		}


		//Close dialog by clicking on backdrop;
		modialog.addEventListener('click', ({
			target: modialog
		}) => {
			if (modialog.nodeName === 'DIALOG') {
				modialog.close('dismiss');
			}
		});

		// Add a listener to any links or buttons with an open_in_dialog class
		function bindOIDs() {
			document.querySelectorAll('.open_in_dialog').forEach(function(oid_button) {
				
				oid_button.addEventListener('click', function(e) {

					e.preventDefault();

					if(oid_button.dataset.reload == 'false'){
						window.modialog_needs_reload = false;
					}else{
						window.modialog_needs_reload = true;
					}
					
					// not sure we should explicitly add the modal=1 here.
					modialog_iframe(oid_button.href + '&modal=1');

				});

			});
		}

		// add a listener so that if we need to we can rebind the open in dialog buttons.
		// in particular if we've passed back some html from HMTX we can rebind the events 
		// using a HX-Trigger-After-Swap header in our response:
		// header('HX-Trigger-After-Swap: bindOIDs');
		document.body.addEventListener('bindOIDs', function() {
			bindOIDs();
		});

		bindOIDs();

	});
</script>