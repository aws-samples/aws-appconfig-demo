<?php

require __DIR__ . '/utils.inc.php';

class ResponseMapper
{
	private $includeReleaseYear;

	public function __construct(bool $includeReleaseYear)
	{
		$this->includeReleaseYear = $includeReleaseYear;
	}

	public function mapResponse($item) {
		if ($this->includeReleaseYear === false) {
			unset($item->year);
		}
		return $item;
	}
}

// Read both the config and data files
$config = readJsonFile(__DIR__ . '/config.json');
$data = readJsonFile(__DIR__ . '/data.json');

// Check if we should throw an exception according to the config
if ($config->errorProbability > mt_rand(0, 100) / 100) {
	sleep(3);
	throw new RuntimeException('Nobody expects Spanish Inquisition! (Or in this case, random entropy)');
}

// Produce a response and return it
$mapper = new ResponseMapper($config->includeReleaseYear);
$response = array_map([$mapper, 'mapResponse'], $data);

header('Content-Type: application/json');
echo(json_encode($response, JSON_PRETTY_PRINT));
