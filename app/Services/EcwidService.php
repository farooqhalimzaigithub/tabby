<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class EcwidService
{
    protected $client;

    public function __construct()
    {
        // Initialize the HTTP client with the base URI from environment variable
        $this->client = new Client([
            'base_uri' => env('ECWID_BASE_URL'), // Your Ecwid base URL, e.g., https://app.ecwid.com/api/v3/
            'timeout'  => 10.0, // Timeout for the request in seconds
        ]);
    }

    /**
     * Fetch an order by its ID from Ecwid.
     *
     * @param string $orderId The ID of the order to fetch.
     * @return array The response from the Ecwid API.
     */
    public function getOrder($orderId)
    {
        try {
            $storeId = env('ECWID_STORE_ID');
            $response = $this->client->get("{$storeId}/orders/{$orderId}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . env('ECWID_SECRET_TOKEN'), // Authorization with the token from environment
                    'Accept' => 'application/json', // Accept JSON response
                ],
            ]);

            // Return the decoded response
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            // Handle errors
            return ['error' => 'Request failed', 'message' => $e->getMessage()];
        }
    }

    /**
     * Update the payment status of an order.
     *
     * @param string $orderId The ID of the order to update.
     * @param string $paymentStatus The new payment status to set.
     * @return array The response from the Ecwid API.
     */
    // public function updateOrderPaymentStatus($orderId, $paymentStatus)
    // {
    //     try {
    //         // Make a POST request to update the payment status
    //         $response = $this->client->post("orders/{$orderId}/payment", [
    //             'headers' => [
    //                 'Authorization' => 'Bearer ' . env('ECWID_SECRET_TOKEN'), // Authorization with the token from environment
    //                 'Accept' => 'application/json', // Accept JSON response
    //                 'Content-Type' => 'application/json', // Content type is JSON
    //             ],
    //             'json' => [
    //                 'paymentStatus' => $paymentStatus, // The payment status to update
    //             ],
    //         ]);

    //         // Return the decoded response
    //         return json_decode($response->getBody(), true);
    //     } catch (RequestException $e) {
    //         // Handle errors
    //         return ['error' => 'Request failed', 'message' => $e->getMessage()];
    //     }
    // }

    public function updateOrderPaymentStatus($orderId, $paymentStatus)
    {
        $storeId = env('ECWID_STORE_ID');
        return $this->makeRequest('PUT', "{$storeId}/orders/{$orderId}", [
            'json' => ['paymentStatus' => $paymentStatus],
        ]);
    }

    private function makeRequest($method, $uri, $data = [])
    {
        try {
            $response = $this->client->request($method, $uri, array_merge([
                'headers' => [
                    'Authorization' => 'Bearer ' . env('ECWID_SECRET_TOKEN'),
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ], $data));

            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : null;

            if ($statusCode === 404) {
                return ['error' => 'Order not found', 'message' => $e->getMessage()];
            } elseif ($statusCode === 401) {
                return ['error' => 'Unauthorized', 'message' => 'Invalid API token or permissions.'];
            }
            return ['error' => 'Request failed', 'message' => $e->getMessage()];
        }
    }


    public function decryptData($encryptedData)
    {
        $decryptionKey = env('ECWID_SECRET_TOKEN');
        
        // Ensure IV is correctly set, e.g., passed along or set as default
        $iv = env('ECWID_DECRYPTION_IV'); // Example IV from .env
        
        return openssl_decrypt($encryptedData, 'aes-256-cbc', $decryptionKey, 0, $iv);
    }
}
