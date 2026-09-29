<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class BaseApiController extends Controller
{
    /**
     * Return standardized success response
     */
    protected function success(mixed $data = null, string $message = 'Success', int $statusCode = 200, array $meta = []): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Return standardized error response
     */
    protected function error(string $message = 'Error', int $statusCode = 400, mixed $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!is_null($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Get current client locale from header
     */
    protected function getLocale(): string
    {
        return request()->header('Accept-Language') === 'en' ? 'en' : 'ar';
    }

    /**
     * Format localized attribute
     */
    protected function localize(mixed $attribute): string
    {
        if (is_array($attribute)) {
            $locale = $this->getLocale();
            return $attribute[$locale] ?? $attribute['ar'] ?? $attribute['en'] ?? '';
        }

        return (string) $attribute;
    }
}
