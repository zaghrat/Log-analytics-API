<?php

namespace App\Service;

class LogParser
{
    private string $logFilePath;

    public function __construct(string $logFilePath)
    {
        $this->logFilePath = $logFilePath;
    }

    public function parse(int $initialPosition, int $count): ?\Generator
    {
        // Open the log file
        $fileHandle = fopen($this->logFilePath, 'r');
        if (!$fileHandle) {
            return null;
        }

        // Read from the file line by line until reaching the desired line
        $currentLine = 0;
        while (!feof($fileHandle) && $currentLine < $initialPosition) {
            fgets($fileHandle); // Read a line and discard it
            $currentLine++;
        }

        $i = 0;
        while ($i < $count && ($line = fgets($fileHandle)) !== false) {
            yield $this->extractData($line);
            $i++;
        }

        fclose($fileHandle);

    }


    private function extractData(string $line): array
    {
        // Define the pattern to match unwanted characters and double spaces
        $pattern = '/[\[\]]/';
        $line = preg_replace($pattern, '', $line);

        $parts = explode(' ', $line);
        $serviceName = $parts[0];
        $statusCode = $parts[count($parts) - 1];
        $requestedAt = \DateTimeImmutable::createFromFormat('d/M/Y:H:i:s O', sprintf("%s %s", $parts[3], $parts[4]));

        return [
            'service_name'  => $serviceName,
            'status_code'   => $statusCode,
            'requestedAt'   => $requestedAt
        ];
    }
}