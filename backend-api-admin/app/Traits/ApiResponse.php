<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Return a success JSON response.
     *
     * @param mixed $data
     * @param string|null $message
     * @param int $code
     * @return JsonResponse
     */
    protected function success(mixed $data = null, ?string $message = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Return a success JSON response with pagination.
     *
     * @param mixed $resource
     * @param string|null $message
     * @return JsonResponse
     */
    protected function successPaginated(mixed $resource, ?string $message = null): JsonResponse
    {
        $response = $resource->response()->getData(true);

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $response['data'],
            'meta' => [
                'current_page' => $response['meta']['current_page'] ?? null,
                'last_page' => $response['meta']['last_page'] ?? null,
                'per_page' => $response['meta']['per_page'] ?? null,
                'total' => $response['meta']['total'] ?? null,
            ],
            'links' => [
                'first' => $response['links']['first'] ?? null,
                'last' => $response['links']['last'] ?? null,
                'prev' => $response['links']['prev'] ?? null,
                'next' => $response['links']['next'] ?? null,
            ],
        ], 200);
    }

    /**
     * Return an error JSON response.
     *
     * @param string $message
     * @param int $code
     * @param mixed $errors
     * @return JsonResponse
     */
    protected function error(string $message, int $code = 400, mixed $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    /**
     * Return a validation error JSON response.
     *
     * @param mixed $errors
     * @param string $message
     * @return JsonResponse
     */
    protected function validationError(mixed $errors, string $message = 'Validasi gagal'): JsonResponse
    {
        return $this->error($message, 422, $errors);
    }

    /**
     * Return a not found JSON response.
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function notFound(string $message = 'Data tidak ditemukan'): JsonResponse
    {
        return $this->error($message, 404);
    }

    /**
     * Return an unauthorized JSON response.
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function unauthorized(string $message = 'Tidak memiliki akses'): JsonResponse
    {
        return $this->error($message, 401);
    }

    /**
     * Return a forbidden JSON response.
     *
     * @param string $message
     * @return JsonResponse
     */
    protected function forbidden(string $message = 'Akses ditolak'): JsonResponse
    {
        return $this->error($message, 403);
    }

    /**
     * Return a created JSON response.
     *
     * @param mixed $data
     * @param string|null $message
     * @return JsonResponse
     */
    protected function created(mixed $data = null, ?string $message = 'Data berhasil dibuat'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

    /**
     * Return a no content JSON response.
     *
     * @param string|null $message
     * @return JsonResponse
     */
    protected function noContent(?string $message = 'Data berhasil dihapus'): JsonResponse
    {
        return $this->success(null, $message, 200);
    }
}
