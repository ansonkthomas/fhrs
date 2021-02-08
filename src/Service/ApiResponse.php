<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponse
{
    /**
     * @var integer HTTP status code - 200 (OK) by default
     */
    protected $statusCode = 200;

    /**
     * Gets the value of statusCode.
     *
     * @return integer
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Sets the value of statusCode.
     *
     * @param integer $statusCode the status code
     *
     * @return self
     */
    protected function setStatusCode($statusCode): self
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Returns a JSON response
     *
     * @param array $data
     * @param array $headers

     * @return JsonResponse
     */
    public function response($data, $headers = []): JsonResponse
    {
        $status = ($this->getStatusCode() == 200) ? "success" : "fail";
        $response = [
            "status" => $status,
            "data" => $data
        ];

        return new JsonResponse($response, $this->getStatusCode(), $headers);
    }

    /**
     * Throw an exception of validation
     *
     * @param array $validate
     * @throws Exception
     */
    public function setValidationStatusCode(): void
    {
        $this->setStatusCode(422);
    }

    /**
     * Throw an exception of bad request
     *
     * @throws Exception
     */
    public function throwBadRequest(): void
    {
        $this->setStatusCode(400);
        throw new \Exception("Does not find the request parameters");
    }

    /**
     * Throw an exception of resource does not found
     *
     * @throws Exception
     */
    public function throwResourceNotFound($message): void
    {
        $this->setStatusCode(404);
        throw new \Exception($message);
    }

    /**
     * 404 response in case of an invalid url. Method defined in framework.yaml
     */
    public function invalidUrl(): JsonResponse
    {
        $this->setStatusCode(404);
        $data = [
            "message" => "The route does not exists or invalid request parameters"
        ];

        return $this->response($data);
    }
}
