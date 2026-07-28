<?php namespace ProcessWire;

/**
 * Export ProcessWire fields and templates to RockMigrations config migration files.
 */
class RockMigrationsExporter extends Wire
{

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

		if ($type === 'templates' && isset($data['fields'])) {
			$data = ['fields' => $data['fields']];
		}

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
