<?php

class {{ controller_name }} extends {{ base_controller_name }}
{
	private $cleanupTargetDir = true;

	/**
	 * You can change the pattern here or rewrite new function to generate final filename
	 */
	private $filePattern = "{{filename_pattern}}";

	private $pathPattern = "{{sub_dir_pattern}}";

	public function uploadAction()
	{
		$uploadFileName = KxFile::getUploadFileName();
		$fileName = KxFile::convertFileName($uploadFileName, $this->filePattern);
		$pathName = KxFile::convertFileName('.', $this->pathPattern);
		$results = KxFile::upload($fileName, $pathName, $this->cleanupTargetDir);

		parent::result($results);
	}
}