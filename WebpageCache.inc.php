<?php

class WebpageCache extends ArrayObject
{
	const ERROR_MISSING_CACHE = 'Could not find cache for %s';
	const ERROR_CANT_CACHE = 'Could not write cache for %s';

	protected $cacheDir = '.';

	public function __construct($cacheDir)
	{
		if (!is_dir($cacheDir) || !is_writable($cacheDir))
		{
			throw new RuntimeException("Cannot open $cacheDir for writing.");
		}

		$this->cacheDir = $cacheDir;
	}

	protected function getCacheFilename($offset)
	{
		return $this->cacheDir . '/' . $offset . '.htmlz';
	}

	public function offsetExists($offset)
	{
		$filename = $this->getCacheFilename($offset);

		return file_exists($filename);
	}

	public function offsetGet($offset)
	{
		// HTML pages are gzipped files.
		$filename = $this->getCacheFilename($offset);
		if (!file_exists($filename))
		{
			throw new RuntimeException(sprintf(self::ERROR_MISSING_CACHE, $offset));
		}

		ob_start();
		readgzfile($filename);
		$data = ob_get_contents();
		ob_end_clean();

		return $data;
	}

	public function offsetSet($offset, $value)
	{
		// HTML pages are gzipped files.
		$filename = $this->getCacheFilename($offset);
		if (($fh = fopen($filename, 'w')) === false)
		{
			throw new RuntimeException(sprintf(self::ERROR_CANT_CACHE, $offset));
		}

		$compressed = gzencode($value);
		fwrite($fh, $compressed);
		fclose($fh);
	}
}

