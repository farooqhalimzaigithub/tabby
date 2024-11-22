<?php

namespace App\Http\Controllers\Tabby;
use App\Http\Controllers\Controller; 
use App\Services\TabyService;
use App\Services\EcwidService;
use Illuminate\Http\Request;

class TabyController extends Controller
{
    protected $tabyService;
    protected $ecwidService;

    public function __construct(TabyService $tabyService ,EcwidService $ecwidService)
    {
        $this->tabyService = $tabyService;
        $this->ecwidService = $ecwidService;
    }

   
        // Structure of request data to send to Tabby API
        public function createSession(Request $request, TabyService $tabyService)
{
    $orderId = "GLZ5R"; // Example Ecwid order ID
    $orderDetails = $this->ecwidService->getOrder($orderId);
        // dd($orderDetails);
    // Validate that the necessary fields are provided
    $requestData = [
        'payment' => [
            'amount' => $orderDetails['total'] ?? 0,  // Ensure this is a valid amount format (string or integer)
            'currency' => 'AED',  // Ensure this is a valid currency format
            'description' => 'Payment for product XYZ',  // Ensure this is a valid description
            'buyer' => [
                'phone' => '03442900411',
                'email' => 'card.success@tabby.ai',
                'name' => 'khan khan 2',
                'dob' => '2019-08-24',
            ],
            'buyer_history' => [
                'registered_since' => '2019-08-24T14:15:22Z',
                'loyalty_level' => 0,
                'wishlist_count' => 0,
                'is_social_networks_connected' => true,
                'is_phone_number_verified' => true,
                'is_email_verified' => true,
            ],
            'order' => [
                'tax_amount' => '0.00',
                'shipping_amount' => '0.00',
                'discount_amount' => '0.00',
                'updated_at' => '2019-08-24T14:15:22Z',
                'reference_id' => 'abc1234',
                'items' => [
                    [
                        'title' => 'my new test product',
                        'description' => 'this is testing product on tabby',
                        'quantity' => 1,
                        'unit_price' => '0.00',
                        'discount_amount' => '0.00',
                        'reference_id' => 'abc1234',
                        'image_url' => 'http://example.com',
                        'product_url' => 'http://example.com',
                        'gender' => 'Male',
                        'category' => 'test category',
                        'color' => 'string',
                        'product_material' => 'string',
                        'size_type' => 'small',
                        'size' => 'low',
                        'brand' => 'warda',
                    ]
                ],
            ],
            'order_history' => [
                [
                    'purchased_at' => '2019-08-24T14:15:22Z',
                    'amount' => '0.00',
                    'payment_method' => 'card',
                    'status' => 'new',
                    'buyer' => [
                        'phone' => '03442900411',
                        'email' => 'card.success@tabby.ai',
                        'name' => 'halimzai',
                        'dob' => '1998-03-29',
                    ],
                    'shipping_address' => [
                        'city' => 'Kohat',
                        'address' => 'Dhoda Road Dhery Banda Kohat',
                        'zip' => '26000',
                    ],
                    'items' => [
                        [
                            'title' => 'mobil',
                            'description' => 'mobile test desc',
                            'quantity' => 1,
                            'unit_price' => '24000.00',
                            'discount_amount' => '2.00',
                            'reference_id' => 'abc1234',
                            'image_url' => 'http://example.com',
                            'product_url' => 'http://example.com',
                            'ordered' => 0,
                            'captured' => 0,
                            'shipped' => 0,
                            'refunded' => 0,
                            'gender' => 'Male',
                            'category' => 'string',
                            'color' => 'string',
                            'product_material' => 'string',
                            'size_type' => 'string',
                            'size' => 'string',
                            'brand' => 'string',
                        ]
                    ],
                ]
            ],
            'shipping_address' => [
                'city' => 'Kohat',
                'address' => 'Dhoda Road Dhery Banda Kohat',
                'zip' => '26000',
            ],
            'meta' => [
                'order_id' => '#1234',
                'customer' => '#customer-id',
            ],
            'attachment' => [
                'body' => '{"flight_reservation_details": {"pnr": "TR9088999","itinerary": [...],"insurance": [...],"passengers": [...],"affiliate_name": "some affiliate"}}',
                'content_type' => 'application/vnd.tabby.v1+json',
            ],
        ],
        'lang' => 'en',
        'merchant_code' => 'Fyrouziare',
        'merchant_urls' => [
                'success' => route('tabby.success', ['order_id' => $orderId]),
                'cancel' => route('tabby.cancel', ['order_id' => $orderId]),
                'failure' => route('tabby.failure', ['order_id' => $orderId]),
            ],
    ];
    //  dd($requestData);
    // Send the request data to Tabby Service
    $response = $tabyService->createCheckoutSession($requestData);
    // dd($response);
    // Check for errors in the response
    if (isset($response['error']) && $response['error'] === true) {
        return redirect()->route('payment.failure')->with('error', $response['message'] ?? 'An error occurred.');
    }

    // Step 2: Check if the response has a "created" status and a web_url
    if ($response['status'] === 'created' && isset($response['configuration']['available_products']['installments'][0]['web_url'])) {
        $webUrl = $response['configuration']['available_products']['installments'][0]['web_url'];
        // Save the payment ID in the session or database (for later verification)
        $paymentId = $response['payment']['id'];

        // Optionally, save the payment ID in the session or database for later use
        session(['payment_id' => $paymentId]);

        // Step 3: Redirect the customer to the Tabby Hosted Payment Page using the web_url
        return redirect($webUrl);
        // return redirect($response['configuration']['available_products']['installments']['web_url']);
    }

    // If no valid web_url is returned, handle the error appropriately
    // return redirect()->route('payment.failure')->with('error', 'Invalid response or missing web_url.');
    return response()->json(['error' => 'Failed to create Tabby session'], 400);

}  



  // Step 2: Handle success callback
  public function successCallback(Request $request)
  {
      $orderId = $request->order_id;

      // Update Ecwid payment status to "PAID"
      $this->ecwidService->updateOrderPaymentStatus($orderId, 'PAID');

      return redirect()->route('order.success')->with('success', 'Payment successful');
  }

  // Step 3: Handle cancel callback
  public function cancelCallback(Request $request)
  {
      $orderId = $request->order_id;

      // Update Ecwid payment status to "CANCELLED"
      $this->ecwidService->updateOrderPaymentStatus($orderId, 'CANCELLED');

      return redirect()->route('order.cancel')->with('error', 'Payment cancelled');
  }

  // Step 4: Handle failure callback
  public function failureCallback(Request $request)
  {
      $orderId = $request->order_id;

      // Update Ecwid payment status to "FAILED"
      $this->ecwidService->updateOrderPaymentStatus($orderId, 'FAILED');

      return redirect()->route('order.failure')->with('error', 'Payment failed');
  }
}
