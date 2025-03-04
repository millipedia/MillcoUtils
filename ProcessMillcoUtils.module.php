<?php
namespace ProcessWire;

/**
 * ProcessMillcoUtils
 * 
 * Admin page for managing various mu settings 
 */

class ProcessMillcoUtils extends Process implements Module
{

	public static function getModuleInfo()
	{
		return [
			'title' => 'Millco Utils Admin',
			'summary' => 'Process to manage Millco Utils',
			'version' => 1,
			'icon' => 'cogs',
			'page' => [
				'name' => 'mu',
				'parent' => 'setup',
				'title' => 'Utils',
				'permission' => 'millco-utils-manage',
			],
			'autoload' => false,
			'singular' => false,
			'permanent' => false,
			'requires' => [
				'MillcoUtils',
			]
		];
	}


	public function init()
	{
		parent::init();
	}

	public function __construct()
	{

	}

	
	public function ___execute()
	{
		// We don't show the manage settings unless you have
		// the millco-utils-manage permissions.
		// TODO - we don't want to show this page at all if you don't have permissions....
		// must work out how.
		if (!(wire('user')->hasPermission('millco-utils-manage'))) {
			return 'You require additional permissions to edit these settings.';
		}

		if ($this->input->post('submit')) {
			$this->mu_save_settings($this->input->post);
		}

		$admin_page_markup = '';

		$moduleConfig = $this->modules->getConfig('MillcoUtils');

		$admin_page_markup .= '<div class="uk-panel" style="margin:2rem 0; padding:2rem;background-color:#eee">';

		$admin_page_markup .= '<div><strong>Processwire Version : </strong>' .  wire('config')->versionName . '</div>';
		$admin_page_markup .= '<div><strong>PHP version : </strong>' . phpversion('tidy') . '</div>';
		if ($_SERVER['REMOTE_ADDR']) {
			$admin_page_markup .= '<div><strong>Server IP address : </strong>' . $_SERVER['REMOTE_ADDR'] . '</div>';
		}
		$panel_info = wire('files')->render(wire('config')->paths->siteModules . 'MillcoUtils/panel_info.php');
		$admin_page_markup .= $panel_info;

		$admin_page_markup .= '</div>';

		/** @var InputfieldForm $form */
		$form = $this->modules->get('InputfieldForm');

		// ====== Edit bar options

		/** @var InputfieldFieldset $fieldset */
		$fieldset = $this->modules->get('InputfieldFieldset');
		$fieldset->label = 'Edit Bar';
		$fieldset->description = 'Set the intial position of the edit toolbar.';
		$fieldset->notes = 'These should be CSS position values. eg. 4px for top and 0px for right will pin the bar to the right hand side of the screen.';
		$fieldset->collapsed = Inputfield::collapsedYes;

		/** @var InputfieldFieldText $field */
		$field = $this->modules->get('InputfieldText');
		$field->name = 'top';
		$field->value = $moduleConfig['top'];
		$field->columnWidth = 20;
		$fieldset->add($field);

		/** @var InputfieldFieldText $field */
		$field = $this->modules->get('InputfieldText');
		$field->name = 'right';
		$field->value = $moduleConfig['right'];
		$field->columnWidth = 20;
		$fieldset->add($field);

		/** @var InputfieldFieldText $field */
		$field = $this->modules->get('InputfieldText');
		$field->name = 'bottom';
		$field->value = $moduleConfig['bottom'];
		$field->columnWidth = 20;
		$fieldset->add($field);


		/** @var InputfieldFieldText $field */
		$field = $this->modules->get('InputfieldText');
		$field->name = 'left';
		$field->value = $moduleConfig['left'];
		$field->columnWidth = 20;
		$fieldset->add($field);

		/** @var InputfieldCheckbox $field */
		$field = $this->modules->get('InputfieldCheckbox');
		$field->name = 'eb_vertical';
		$field->label = 'Vertical edit bar';
		$field->value = 1;
		if ($moduleConfig['eb_vertical']) {
			$field->checked(true);
		}
		//$field->autocheck=1;
		$field->columnWidth = 20;
		$fieldset->add($field);


		/** @var InputfieldFieldTextArea $field */
		$field = $this->modules->get('InputfieldTextArea');
		$field->label = 'Additional buttons';
		$field->name = 'extra_buttons';
		$field->description = 'Enter additional links to add to the bar. These should be in the format url,label,icon and then an optional role eg /admin/setup/mu/,Utils,settings,editor';

		// Get list of available icons from our
		// MillcoUtils/icons directory
		$icons_array = [];
		$icons_directory = wire('config')->paths->siteModules . 'MillcoUtils/icons/';
		foreach (new \DirectoryIterator($icons_directory) as $file) {
			if ($file->isFile()) {

				if($file->getExtension() == 'svg'){
					$icons_array[] = $file->getBasename('.svg');
				}
			}
		}

		sort($icons_array); // I always think DirectoryIterator should be able to do this.

		$icons_list = implode(', ', $icons_array);

		$fieldset->notes = 'Current available icons: ' . $icons_list . ' - You can add new editbar svg icons to the MillcoUtils/icons folder.';

		$field->value = $moduleConfig['extra_buttons'];
		$field->columnWidth = 100;
		$fieldset->add($field);
		$form->add($fieldset);


		// ======  Inline images

		/** @var InputfieldFieldset $fieldset */
		$fieldset = $this->modules->get('InputfieldFieldset');
		$fieldset->label = 'Inline images';
		$fieldset->description = '';
		$fieldset->notes = '';
		$fieldset->collapsed = Inputfield::collapsedYes;

		/** @var InputfieldText $field */
		$field = $this->modules->get('InputfieldText');
		$field->label = 'Path to inline images';
		$field->description = 'This is relative to the assets/images folder.';
		$field->notes = 'This used to be a folder called \'icons\' so if you\'ve updated from a previous version of this module then check things are working as expected.';
		$field->name = 'inline_image_path';
		$field->value = $moduleConfig['inline_image_path'];
		$field->columnWidth = 100;
		$fieldset->add($field);

		$form->add($fieldset);

		// ======  Social media links

		/** @var InputfieldFieldset $fieldset */
		$fieldset = $this->modules->get('InputfieldFieldset');
		$fieldset->label = 'Social media';
		$fieldset->description = 'Enter your social media user names.';
		$fieldset->notes = 'We don\'t do anything with these values but they\'re there if you want them.';
		$fieldset->collapsed = Inputfield::collapsedYes;
	
		/** @var InputfieldText $field */
		$field = $this->modules->get('InputfieldText');
		$field->name = 'bluesky';
		$field->label = 'Bluesky';
		$field->value = $moduleConfig['bluesky'];
		$field->columnWidth = 25;
		$fieldset->add($field);

		/** @var InputfieldText $field */
		$field = $this->modules->get('InputfieldText');
		$field->name = 'youtube';
		$field->label = 'Youtube';
		$field->value = $moduleConfig['youtube'];
		$field->columnWidth = 25;
		$fieldset->add($field);

		/** @var InputfieldText $field */
		$field = $this->modules->get('InputfieldText');
		$field->name = 'facebook';
		$field->label = 'Facebook';
		$field->value = $moduleConfig['facebook'];
		$field->columnWidth = 25;
		$fieldset->add($field);

		/** @var InputfieldText $field */
		$field = $this->modules->get('InputfieldText');
		$field->name = 'instagram';
		$field->label = 'Instagram';
		$field->value = $moduleConfig['instagram'];
		$field->columnWidth = 25;
		$fieldset->add($field);

		$form->add($fieldset);

		// ====== Analytics options

		/** @var InputfieldFieldset $fieldset */
		$fieldset = $this->modules->get('InputfieldFieldset');
		$fieldset->label = 'Analytics';
		$fieldset->description = 'Automatically add analytics tags.';
		$fieldset->notes = '';
		$fieldset->collapsed = Inputfield::collapsedYes;

		/** @var InputfieldCheckbox $field */
		$field = $this->modules->get('InputfieldCheckbox');
		$field->name = 'analytics_in_dev';
		$field->label = 'Always include anlytics tag';
		$field->notes = 'By default we dont add tags if the site is in dev mode.';
		$field->value = 1;
		if ($moduleConfig['analytics_in_dev']) {
			$field->checked(true);
		}
		//$field->autocheck=1;
		$field->columnWidth = 25;
		$fieldset->add($field);

		/** @var InputfieldText $field */
		$field = $this->modules->get('InputfieldText');
		$field->name = 'fathom';
		$field->label = 'Add a Fathom site code';
		$field->notes = 'This will something like <em>GZRYZGYC</em>';
		$field->value = $moduleConfig['fathom'];
		$field->columnWidth = 75;
		$fieldset->add($field);

		/** @var InputfieldCheckbox $field */
		$field = $this->modules->get('InputfieldCheckbox');
		$field->name = 'cabin';
		$field->label = 'Include Cabin analytics tag';
		$field->notes = 'NB you need to configure a Cabin account for this domain.';
		$field->value = 1;
		if ($moduleConfig['cabin']) {
			$field->checked(true);
		}
		//$field->autocheck=1;
		$field->columnWidth = 25;
		$fieldset->add($field);

		/** @var InputfieldText $field */
		$field = $this->modules->get('InputfieldText');
		$field->name = 'cabin_custom';
		$field->label = 'Add a Cabin custom domain';
		$field->value = $moduleConfig['cabin_custom'];
		$field->notes = 'If you use a custom domain on Cabin eg cabin.millipedia.net then enter it here.';
		$field->columnWidth = 75;
		$fieldset->add($field);

		$form->add($fieldset);

		/** @var InputfieldSubmit $button */
		$button = $this->modules->get('InputfieldSubmit');
		$button->value = 'Save';
		$button->icon = 'floppy-o';

		$form->add($button);

		$admin_page_markup .= $form->render();

		return $admin_page_markup;
	}

	/**
	 * Update our module config settings with the submitted
	 * values.
	 * 
	 * @param Object $post_data
	 */
	public function mu_save_settings($post_data)
	{

		// Get the current config data as an array
		// This appears to be only the saved data.
		$mu_config_data = $this->modules->getConfig('MillcoUtils');

		// Get an array of all the config options we can have.
		/** @var MillcoUtils $mu_instance */
		$mu_instance = $this->modules->get('MillcoUtils');
		$mu_config_defaults =$mu_instance->get_defaults();

		// loop through our possible config options
		// and if we have posted value then update it.

		foreach($mu_config_defaults as $index => $value){

			// if we have a posted value then update our value with that
			// TODO should I be sanitizing these really...
			// I suspect I'm doing this in a slightly odd way.
			 if(isset($post_data[$index])){
				$value=$post_data[$index];
			 }

			 $mu_config_data[$index]=$value;

		}

		$this->message('Settings saved'); // TODO check we have actually updated something.

		$this->modules->saveConfig('MillcoUtils', $mu_config_data);

		// reload our utils page
		/** @var Wire $this */
		$this->session->redirect('./');
	}


}
