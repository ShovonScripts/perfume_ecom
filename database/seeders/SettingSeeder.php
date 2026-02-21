<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::set('shipping_inside_dhaka_fee', 70);
        Setting::set('shipping_outside_dhaka_fee', 130);

        // Branding defaults
        Setting::set('site_name', 'Perfume Store');
        Setting::set('footer_description', 'Luxury fragrances delivered across Bangladesh.');
        Setting::set('facebook_url', '');
        Setting::set('instagram_url', '');
        Setting::set('youtube_url', '');
        Setting::set('support_phone', '');
        Setting::set('support_email', '');

        // Payment Settings
        Setting::set('bkash_number', '017XXXXXXXX');
        Setting::set('nagad_number', '018XXXXXXXX');
    }
}
