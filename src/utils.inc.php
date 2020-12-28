<?php

function readJsonFile(string $path) {
	$rawData = file_get_contents($path);
	if ($rawData === false) {
		throw new RuntimeException("File '{$path}' does not exist");
	}

	$data = json_decode($rawData);
	if ($data === null) {
		throw new RuntimeException("File '{$path}' is not valid JSON");
	}

	return $data;
}

function writeJsonFile(string $path, $data) {
	$encodedData = json_encode($data, JSON_PRETTY_PRINT);
	if ($encodedData === null) {
		throw new RuntimeException("Data could not be converted to JSON");
	}

	if (file_put_contents($path, $encodedData) === false) {
		throw new RuntimeException("Data could not be written to '{$path}'");
	};
}
