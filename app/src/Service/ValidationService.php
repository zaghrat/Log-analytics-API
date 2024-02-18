<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;

class ValidationService
{

    private const VALID_STATUS_CODES = [
        100, 101, 102, 103,
        200, 201, 202, 204, 206,
        300, 301, 302, 303, 304, 307, 308,
        400, 401, 403, 404, 405, 406, 408, 409, 410, 413, 414, 415, 429,
        500, 501, 502, 503, 504, 505, 511
    ];
    public function validateQueryParameters(Request $request): array
    {
        $errors = [];

        // Validate serviceNames parameter
        $serviceNames = $request->get('serviceNames');
        if ($serviceNames && !is_array($serviceNames)) {
            $errors[] = 'Parameter serviceNames must be an array.';
        }

        // Validate statusCode parameter
        $statusCode = (int)$request->query->get('statusCode', 0);
        if ($statusCode && !in_array($statusCode, self::VALID_STATUS_CODES) ) {
            $errors[] = 'Parameter statusCode must be a valid HTTP status code.';
        }

        // Validate startDate parameter
        $startDate = $request->query->get('startDate');
        if ($startDate !== null) {
            $date = \DateTime::createFromFormat('Y-m-d', $startDate);
            if (!$date || $date->format('Y-m-d') !== $startDate) {
                $errors[] = 'Parameter "startDate" is not a valid date or is not in the format YYYY-MM-DD.';
            }
        }

        // Validate endDate parameter
        $endDate = $request->query->get('endDate');
        if ($endDate !== null) {
            $date = \DateTime::createFromFormat('Y-m-d', $endDate);
            if (!$date || $date->format('Y-m-d') !== $endDate) {
                $errors[] = 'Parameter "endDate" is not a valid date or is not in the format YYYY-MM-DD.';
            }
        }

        return $errors;
    }
}