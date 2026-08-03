<?php namespace ProcessWire;

/**
 * Export ProcessWire fields and templates to RockMigrations config migration files.
 */
class RockMigrationsExporter extends Wire
{

	/**
	 * Field properties holding IDs that belong to a single install
	 */
	const localIdKeys = ['parent_id', 'template_id', 'template_ids'];

	/**
	 * Export a field or template to site/RockMigrations/{type}/{name}.php
	 *
	 * @param string $type fields|templates
	 * @param string $name Field or template name
	 * @param bool $overwrite Replace an existing migration file
	 * @return string Absolute path to the written file
	 */
	public function export(string $type, string $name, bool $overwrite = false): string
	{
		$type = $this->sanitizer->fieldName($type);
		$name = $this->sanitizer->fieldName($name);

		if (!in_array($type, ['fields', 'templates'], true)) {
			throw new WireException('Type must be fields or templates');
		}
		if ($name === '') {
			throw new WireException('Name is required');
		}

		/** @var RockMigrations|null $rm */
		$rm = $this->modules->get('RockMigrations');
		if (!$rm) {
			throw new WireException('RockMigrations is not installed');
		}

		if ($type === 'fields') {
			$item = $this->fields->get($name);
			$label = 'field';
		} else {
			$item = $this->templates->get($name);
			$label = 'template';
		}

		if (!$item || !$item->id) {
			throw new WireException(ucfirst($label) . " '$name' not found");
		}

		$data = $rm->getCode($item, 2);
		if (!is_array($data)) {
			throw new WireException("Could not export $label '$name'");
		}

		if ($type === 'fields') {
			$data = $this->pruneLocalIds($item, $data);
		} elseif ($type === 'templates') {
			// fieldgroups_id mirrors the template name; fields are set via the fields key
			unset($data['fieldgroups_id']);
		}

		$data = $this->pruneDefaults($item, $data);
		$data = $this->pruneEmpty($data);

		$dir = $this->config->paths->site . "RockMigrations/$type/";
		$file = $dir . $name . '.php';

		if (is_file($file) && !$overwrite) {
			throw new WireException("File already exists: $file (enable overwrite to replace it)");
		}

		$this->files->mkdir($dir);

		$exportedAt = $this->datetime->date('Y-m-d H:i');
		$exportedBy = $this->user->name ?: 'unknown';
		$body = $rm->varexport($data);

		$php = "<?php namespace ProcessWire;\n\n";
		$php .= "/**\n";
		$php .= " * RockMigrations config migration for the $name $label.\n";
		$php .= " *\n";
		$php .= " * Exported from ProcessWire on $exportedAt by $exportedBy.\n";
		$php .= " */\n\n";
		$php .= "return $body;\n";

		$this->files->filePutContents($file, $php);

		return $file;
	}

	/**
	 * Remove properties that still match ProcessWire defaults for a new item of this type.
	 *
	 * Always keeps:
	 * - templates: fields (field assignments / context)
	 * - fields: type (required for RockMigrations to create the field)
	 *
	 * @param Field|Template $item
	 * @param array $data
	 * @return array
	 */
	public function pruneDefaults(Field|Template $item, array $data): array
	{
		$defaults = $this->getDefaultData($item);
		$alwaysKeep = $item instanceof Template
			? ['fields']
			: ['type'];

		foreach ($data as $key => $value) {
			if (in_array($key, $alwaysKeep, true)) continue;
			if (!array_key_exists($key, $defaults)) continue;
			if ($this->isDefaultValue($value, $defaults[$key])) {
				unset($data[$key]);
			}
		}

		// Template files declaring `namespace ProcessWire;` store ns as ProcessWire;
		// that is the normal case, so omit it from migrations.
		if (($data['ns'] ?? null) === 'ProcessWire') {
			unset($data['ns']);
		}

		return $data;
	}

	/**
	 * Defaults for a brand-new Template or Field of the same type.
	 *
	 * @param Field|Template $item
	 * @return array
	 */
	protected function getDefaultData(Field|Template $item): array
	{
		if ($item instanceof Template) {
			/** @var Template $blank */
			$blank = $this->wire(new Template());
			$defaults = $blank->getTableData();
			if (isset($defaults['data']) && is_array($defaults['data'])) {
				$defaults = array_merge($defaults, $defaults['data']);
			}
			unset(
				$defaults['data'],
				$defaults['id'],
				$defaults['name'],
				$defaults['modified'],
				$defaults['fieldgroups_id'],
			);
			return $defaults;
		}

		/** @var Field $blank */
		$blank = $this->wire(new Field());
		$blank->type = $item->type;
		$inputfieldClass = $item->get('inputfieldClass');
		if ($inputfieldClass) {
			$blank->set('inputfieldClass', $inputfieldClass);
		}

		$defaults = $blank->getExportData();
		unset($defaults['id'], $defaults['name']);

		return $defaults;
	}

	/**
	 * Whether an exported value matches the ProcessWire default.
	 *
	 * Mirrors ProcessWire's import comparison: exact match, loose match, or both empty.
	 *
	 * @param mixed $value
	 * @param mixed $default
	 * @return bool
	 */
	protected function isDefaultValue(mixed $value, mixed $default): bool
	{
		if ($value === $default) return true;
		if ($value == $default) return true;
		if (empty($value) && empty($default)) return true;

		if (is_array($value) && is_array($default)) {
			return array_values($value) === array_values($default);
		}

		return false;
	}

	/**
	 * Remove install-local ID properties from field export data.
	 *
	 * Repeater derived fields (Repeater, RepeaterMatrix, FieldsetPage) keep the ID of
	 * their generated /processwire/repeaters/ parent and template in parent_id and
	 * template_id. ProcessWire zeroes both when exporting config data, and migrating a
	 * zero back onto the field detaches it from its storage, so they are always dropped.
	 *
	 * Other fieldtypes can use the same keys for real config — a Page field's selectable
	 * parent, which ProcessWire exports as a portable path — so there only meaningless
	 * zero values are dropped.
	 *
	 * @param Field $field
	 * @param array $data
	 * @return array
	 */
	public function pruneLocalIds(Field $field, array $data): array
	{
		$isRepeater = wireInstanceOf($field->type, 'FieldtypeRepeater');

		foreach (self::localIdKeys as $key) {
			if (!array_key_exists($key, $data)) continue;

			if ($isRepeater) {
				unset($data[$key]);
				continue;
			}

			$value = $data[$key];
			if ($value === 0 || $value === '0' || $value === []) unset($data[$key]);
		}

		return $data;
	}

	/**
	 * Remove empty values from export data.
	 *
	 * Template field assignments use an empty array to mean "no context overrides"
	 * and are preserved when they are direct children of a fields array.
	 *
	 * @param array $data
	 * @param bool $isTemplateFieldsList
	 * @return array
	 */
	public function pruneEmpty(array $data, bool $isTemplateFieldsList = false): array
	{
		$result = [];

		foreach ($data as $key => $value) {
			if (is_array($value)) {
				if ($isTemplateFieldsList && $value === []) {
					$result[$key] = [];
					continue;
				}

				$childIsTemplateFields = ($key === 'fields');
				$pruned = $this->pruneEmpty($value, $childIsTemplateFields);

				if ($pruned === []) continue;

				$result[$key] = $pruned;
				continue;
			}

			if ($value === '' || $value === null) continue;

			$result[$key] = $value;
		}

		return $result;
	}

	/**
	 * Get field and template names for export UI selectors.
	 *
	 * @return array{fields: string[], templates: string[]}
	 */
	public function getExportableNames(): array
	{
		$fields = [];
		foreach ($this->fields as $field) {
			$fields[] = $field->name;
		}
		sort($fields, SORT_NATURAL);

		$templates = [];
		foreach ($this->templates as $template) {
			$templates[] = $template->name;
		}
		sort($templates, SORT_NATURAL);

		return [
			'fields' => $fields,
			'templates' => $templates,
		];
	}

}
