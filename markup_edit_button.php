<?php

/**
 * 
 * This writes out the html for the edit bar
 * along with styles and some js to make it draggable.
 * 
 */

namespace ProcessWire;

/***
 * load an icon inline from an svg file in MillcoUtils/icons
 * Currently the icons in there are from
 * https://icon-sets.iconify.design/carbon/
 * 
 */

function mu_editbar_icon($filename)
{

	$path = wire('config')->paths->siteModules . 'MillcoUtils/icons/';
	$filename = $path . $filename . '.svg';

	if ($icon = file_get_contents($filename)) {
		return $icon;
	} else {
		return $filename;
	}
}

$moduleConfig = wire('modules')->get('MillcoUtils');

$eb_position = '';
$eb_classes = '';

if ($moduleConfig->top) {
	$eb_position .= 'top: ' . $moduleConfig->top . ';';
}
if ($moduleConfig->right) {
	$eb_position .= 'right: ' . $moduleConfig->right . ';';
}
if ($moduleConfig->bottom) {
	$eb_position .= 'bottom: ' . $moduleConfig->bottom . ';';
}
if ($moduleConfig->left) {
	$eb_position .= 'left: ' . $moduleConfig->left . ';';
}

if ($eb_position == '') {
	$eb_position = 'top:4px;right:4px;';
}

if ($moduleConfig->eb_vertical) {
	$eb_classes .= ' mu_eb_vertical';
}

?>

<style nonce="<?= $page->nonce ?>">
	.mu_edit_bar {
		position: fixed;
		display: flex;
		gap: 0.75rem;
		border: 1px solid #ccc;
		border-radius: 4px;
		background-color: #222;
		padding: 4px 8px;
		color: white;
		box-shadow: 2px 2px 4px #666;
		z-index: 999;
		cursor: move;
		<?php echo $eb_position; ?>
	}

	.mu_edit_bar a {
		display: flex;
		flex-direction: column;
		text-decoration: none;
		font-size: 11px;
		line-height: 1.1;
		align-items: center;
		justify-content: center;
		max-width: fit-content;
		cursor: pointer;
		color: white;
	}

	.mu_eb_vertical {
		flex-direction: column;
	}

	.mu_eb_vertical a {
		flex-direction: row;
		gap: 0.25rem;
		font-size: 12px;
		white-space: nowrap;
	}

	.mu_edit_bar a:hover {
		color: var(--accent, #D6363A);
	}

	.mu_edit_bar a svg {
		width: 100%;
		width: 20px;
		height: auto;
	}

	@media print {
		.mu_edit_bar {
			display: none !important;
		}
	}
</style>

<div id="mu_edit_bar" class="mu_edit_bar <?= $eb_classes ?>">

	<?php

	echo '<a href="' . $page->editURL . '" class="no_external_link">' . mu_editbar_icon('edit') . 'Edit</a>';
	echo '<a href="' . $urls->admin . 'page" class="no_external_link">' . mu_editbar_icon('pages') . 'Pages</a>';

	// additional buttons
	if ($moduleConfig->extra_buttons) {

		$ebs = explode(PHP_EOL, $moduleConfig->extra_buttons);

		// additional buttons are just a comma separated string in the 
		// format url,label,icon,role

		if (is_array($ebs)) {

			foreach ($ebs as $eb) {

				$eb_array = explode(',', $eb);

				if (is_array($eb_array)) {

					// if we have a 4th item then check to see if the current
					// user has that role.
					if (isset($eb_array[3]) && $eb_array[3] != '') {

						$role = $sanitizer->pageName($eb_array[3]);

						if ($user->hasRole($role)) {
							$show_button = 1;
						} else {
							$show_button = 0;
						}
					} else {

						$show_button = 1;
					}

					if ($show_button) {

						echo '<a href="' . $eb_array[0] . '" class="no_external_link">';

						// pull in an icon from our icons collection.
						$iconname = trim($eb_array[2]);
						echo mu_editbar_icon($iconname);
						echo '<span class="mu_eb_label">' . $eb_array[1] . '</span>';
						echo '</a>';
					}
				}
			}
		}
	}

	// only superusers get to see templates
	if ($user->isSuperuser()) {

		echo '<a href="' . $pages->get(2)->url . 'setup/template/edit?id=' . $page->template->id . '">';
		echo mu_editbar_icon('template');
		echo '{' . $page->template->name . '}';
		echo '</a>';
	} // close is super user conditional

	?>


</div>

<script nonce="<?= $page->nonce ?>">
	const edit_bar = document.getElementById("mu_edit_bar");

	// Make the edit bar draggable:
	// This is pretty much just from.
	// https://www.w3schools.com/howto/howto_js_draggable.asp
	dragElement(edit_bar);

	function dragElement(elmnt) {
		var pos1 = 0,
			pos2 = 0,
			pos3 = 0,
			pos4 = 0;

		elmnt.onmousedown = dragMouseDown;

		function dragMouseDown(e) {
			e = e || window.event;
			e.preventDefault();
			// get the mouse cursor position at startup:
			pos3 = e.clientX;
			pos4 = e.clientY;
			document.onmouseup = closeDragElement;
			// call a function whenever the cursor moves:
			document.onmousemove = elementDrag;
		}

		function elementDrag(e) {
			e = e || window.event;
			e.preventDefault();
			// calculate the new cursor position:
			pos1 = pos3 - e.clientX;
			pos2 = pos4 - e.clientY;
			pos3 = e.clientX;
			pos4 = e.clientY;
			// set the element's new position:
			elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
			elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
			// because we're just using top and left to position the bar
			// we need to clear right and bottom values which might have been set
			// by the user.
			elmnt.style.right = '';
			elmnt.style.bottom = '';
		}

		function closeDragElement() {
			// stop moving when mouse button is released:
			document.onmouseup = null;
			document.onmousemove = null;
		}
	}
</script>