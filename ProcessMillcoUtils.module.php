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
				'PHP>=8.0.0',
				'ProcessWire>=3.0.0',
				'MillcoUtils',
			]
		];
	}


	static protected $defaults = array(
		'top' => '',
		'right' => '',
		'bottom' => '',
		'left' => '',
		'eb_vertical' => '0',
		'twitter' => '',
		'youtube' => '',
		'facebook' => '',
		'instagram' => '',
		'extra_buttons' => ''
	);
	


	public function init()
	{
		parent::init();
	}

	public function __construct()
	{
		// populate defaults, which will get replaced with actual
		// configured values before the init/ready methods are called
		$this->setArray(self::$defaults);
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

		$moduleConfig = $this->modules->getModuleConfigData('MillcoUtils');

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
		// $form->action = './Mu_save_settings';

		/** @var InputfieldFieldset $fieldset */
		$fieldset = $this->modules->get('InputfieldFieldset');
		$fieldset->label = 'Edit Bar';
		$fieldset->description = 'Set the intial position of the edit toolbar.';
		$fieldset->notes = 'These should be CSS position values. eg. 4px for top and 0px for right will pin the bar to the right hand side of the screen.';
		$fieldset->collapsed = Inputfield::collapsedYes;

		$field = $this->modules->get('InputfieldText');
		$field->name = 'top';
		$field->value = $moduleConfig['top'];
		$field->columnWidth = 20;
		$fieldset->add($field);

		$field = $this->modules->get('InputfieldText');
		$field->name = 'right';
		$field->value = $moduleConfig['right'];
		$field->columnWidth = 20;
		$fieldset->add($field);

		$field = $this->modules->get('InputfieldText');
		$field->name = 'bottom';
		$field->value = $moduleConfig['bottom'];
		$field->columnWidth = 20;
		$fieldset->add($field);

		$field = $this->modules->get('InputfieldText');
		$field->name = 'left';
		$field->value = $moduleConfig['left'];
		$field->columnWidth = 20;
		$fieldset->add($field);

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

		$fieldset->notes = 'Current availabele icons: ' . $icons_list . ' - You can add new editbar svg icons to the MillcoUtils/icons folder.';

		$field->value = $moduleConfig['extra_buttons'];
		$field->columnWidth = 100;
		$fieldset->add($field);



		$form->add($fieldset);

		/** @var InputfieldFieldset $fieldset */
		$fieldset = $this->modules->get('InputfieldFieldset');
		$fieldset->label = 'Social media';
		$fieldset->description = 'Enter your social media user names.';
		$fieldset->notes = '';
		$fieldset->collapsed = Inputfield::collapsedYes;

		$field = $this->modules->get('InputfieldText');
		$field->name = 'twitter';
		$field->value = $moduleConfig['twitter'];
		$field->columnWidth = 25;
		$fieldset->add($field);

		$field = $this->modules->get('InputfieldText');
		$field->name = 'youtube';
		$field->value = $moduleConfig['youtube'];
		$field->columnWidth = 25;
		$fieldset->add($field);

		$field = $this->modules->get('InputfieldText');
		$field->name = 'facebook';
		$field->value = $moduleConfig['facebook'];
		$field->columnWidth = 25;
		$fieldset->add($field);

		$field = $this->modules->get('InputfieldText');
		$field->name = 'instagram';
		$field->value = $moduleConfig['instagram'];
		$field->columnWidth = 25;
		$fieldset->add($field);

		$form->add($fieldset);

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
		$mu_config_data = $this->modules->getConfig('MillcoUtils');

		// TODO sanitize all these better.
		$top = $post_data->top;
		$mu_config_data['top'] = $top;

		$right = $post_data->right;
		$mu_config_data['right'] = $right;

		$bottom = $post_data->bottom;
		$mu_config_data['bottom'] = $bottom;

		$left = $post_data->left;
		$mu_config_data['left'] = $left;

		$extra_buttons = $post_data->extra_buttons;
		$mu_config_data['extra_buttons'] = $extra_buttons;

		$eb_vertical = $post_data->eb_vertical;

		$mu_config_data['eb_vertical'] = $eb_vertical;

		$twitter = $post_data->twitter;
		$mu_config_data['twitter'] = $twitter;

		$facebook = $post_data->facebook;
		$mu_config_data['facebook'] = $facebook;

		$youtube = $post_data->youtube;
		$mu_config_data['youtube'] = $youtube;

		$instagram = $post_data->instagram;
		$mu_config_data['instagram'] = $instagram;

		$this->message('Settings saved'); // TODO check we have actually updated something.

		$this->modules->saveConfig('MillcoUtils', $mu_config_data);

		// reload our utils page
		/** @var Wire $this */
		$this->session->redirect('./');
	}


}
