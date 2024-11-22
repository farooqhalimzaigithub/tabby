<?php

namespace App\Services;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class TabyService
{
    protected $client;

    public function __construct()
    {
        // Initialize the Guzzle client
        $this->client = new Client([
            'base_uri' => env('TABY_BASE_URL', 'https://api.tabby.ai/api/v2/'), // Base URL for Tabby API
            'timeout'  => 10.0, // Request timeout in seconds
        ]);
    }

    /**
     * Create a checkout session in Tabby API
     * 
     * @param array $data
     * @return array
     */
    public function createCheckoutSession(array $data)
    {
        try {
           
            // Sending a POST request to Tabby API to create a checkout session
            $response = $this->client->post('checkout', [
                'headers' => [
                    'Authorization' => 'Bearer ' . env('TABY_PUBLIC_KEY'), // Use your public key from .env
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json', 
                ],
                'json' => $data,
            ]);

            // Check for successful response (200 OK)
            if ($response->getStatusCode() == 200) {
                // Parse and return the JSON response
                return json_decode($response->getBody(), true);
            }

            // If the response code isn't 200, return an error message
            return [
                'error' => true,
                'message' => 'Unexpected response status: ' . $response->getStatusCode(),
            ];

        } catch (RequestException $e) {
            // Handle Guzzle exceptions and return an error
            return [
                'error' => true,
                'message' => 'Request failed: ' . $e->getMessage(),
            ];
        } catch (\Exception $e) {
            // Handle any other general exceptions
            return [
                'error' => true,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get a checkout session by its ID
     * 
     * @param string $id
     * @return array
     */
   
}
