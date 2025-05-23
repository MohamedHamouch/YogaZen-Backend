<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Course;

class WooCommerceWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $topic = $request->header('X-WC-Webhook-Topic');
        $data = $request->all();

        Log::info('WooCommerce webhook received', ['topic' => $topic, 'data' => $data]);

        try {
            switch ($topic) {
                case 'order.created':
                case 'order.updated':
                    $this->handleOrderWebhook($data);
                    break;
                
                case 'product.created':
                case 'product.updated':
                    $this->handleProductWebhook($data);
                    break;
                
                default:
                    Log::warning('Unknown webhook topic', ['topic' => $topic]);
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Webhook processing failed', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error'], 500);
        }
    }

    private function handleOrderWebhook($data)
    {
        Subscription::updateOrCreate(
            ['id' => $data['id']],
            [
                'customer_email' => $data['billing']['email'],
                'status' => $data['status'],
                'total' => $data['total'],
                'line_items' => $data['line_items']
            ]
        );

    }

    private function handleProductWebhook($data)
    {
        Course::updateOrCreate(
            ['id' => $data['id']],
            [
                'name' => $data['name'],
                'description' => $data['description'],
                'price' => $data['price'],
                'status' => $data['status']
            ]
        );

    }
}