<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ConfigController extends Controller
{
    public function index()
    {
        $settings = DB::table('settings')->pluck('value', 'key');
        
        return response()->json([
            'max_photos' => isset($settings['max_photos']) ? (int)$settings['max_photos'] : 5,
            'maintenance_mode' => isset($settings['maintenance_mode']) ? filter_var($settings['maintenance_mode'], FILTER_VALIDATE_BOOLEAN) : false,
            'listing_active_days' => isset($settings['listing_active_days']) ? (int)$settings['listing_active_days'] : 30,
            'low_stock_threshold' => isset($settings['low_stock_threshold']) ? (int)$settings['low_stock_threshold'] : 3,
            'report_reasons' => [
                'Penipuan',
                'Barang terlarang',
                'Sengketa transaksi',
                'Perilaku pengguna',
                'Foto tidak sesuai',
                'Spam chat',
                'Lainnya'
            ],
            'allowed_email_domains' => isset($settings['allowed_email_domains']) ? $settings['allowed_email_domains'] : ''
        ]);
    }
}
