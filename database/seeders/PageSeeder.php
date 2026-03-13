<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'about',
                'title' => 'About ATL Ticket Exchange',
                'body' => '<h2>About Us</h2><p>ATL Ticket Exchange is Atlanta\'s premier peer-to-peer ticket marketplace. We connect buyers and sellers of Atlanta sports and event tickets directly — no middleman, no excessive fees.</p><p>Our community is built on trust, transparency, and a shared love of Atlanta sports and entertainment.</p>',
                'meta_title' => 'About ATL Ticket Exchange',
                'meta_description' => 'Learn about ATL Ticket Exchange — Atlanta\'s peer-to-peer ticket marketplace for sports and events.',
                'is_published' => true,
            ],
            [
                'slug' => 'how-it-works',
                'title' => 'How It Works',
                'body' => '<h2>How ATL Ticket Exchange Works</h2><h3>For Sellers</h3><ol><li>Create a free account and list your tickets.</li><li>Connect with interested buyers through our messaging system.</li><li>Arrange payment directly via Venmo, Cash App, Zelle, or in person.</li><li>Transfer tickets and leave feedback.</li></ol><h3>For Buyers</h3><ol><li>Browse listings for your favorite team or event.</li><li>Contact the seller through our secure messaging system.</li><li>Agree on payment and transfer method.</li><li>Leave feedback to help build community trust.</li></ol>',
                'meta_title' => 'How It Works — ATL Ticket Exchange',
                'meta_description' => 'Learn how to buy and sell Atlanta event tickets on ATL Ticket Exchange.',
                'is_published' => true,
            ],
            [
                'slug' => 'terms',
                'title' => 'Terms of Service',
                'body' => '<h2>Terms of Service</h2><p>By using ATL Ticket Exchange, you agree to these terms. This platform facilitates connections between buyers and sellers; we do not process payments or guarantee transactions.</p><p>Users are responsible for ensuring the authenticity of tickets listed. Fraudulent activity will result in immediate banning.</p><p>Please review the full terms before using the platform.</p>',
                'meta_title' => 'Terms of Service — ATL Ticket Exchange',
                'meta_description' => 'Review the Terms of Service for ATL Ticket Exchange.',
                'is_published' => true,
            ],
            [
                'slug' => 'privacy',
                'title' => 'Privacy Policy',
                'body' => '<h2>Privacy Policy</h2><p>ATL Ticket Exchange takes your privacy seriously. We collect only the information necessary to operate the platform and never sell your personal data to third parties.</p><p>We use industry-standard security measures to protect your information.</p>',
                'meta_title' => 'Privacy Policy — ATL Ticket Exchange',
                'meta_description' => 'Read the ATL Ticket Exchange Privacy Policy.',
                'is_published' => true,
            ],
            [
                'slug' => 'faq',
                'title' => 'Frequently Asked Questions',
                'body' => '<h2>Frequently Asked Questions</h2><h3>Is this site affiliated with any Atlanta sports teams?</h3><p>No. ATL Ticket Exchange is an independent peer-to-peer marketplace and is not affiliated with any team, venue, or ticketing company.</p><h3>How do I pay for tickets?</h3><p>Payment is arranged directly between buyer and seller. We recommend Venmo, Cash App, Zelle, or in-person cash transactions.</p><h3>What if I have a problem with a transaction?</h3><p>You can leave negative feedback and report the user to our moderation team. We take all reports seriously.</p>',
                'meta_title' => 'FAQ — ATL Ticket Exchange',
                'meta_description' => 'Frequently asked questions about ATL Ticket Exchange.',
                'is_published' => true,
            ],
        ];

        foreach ($pages as $page) {
            Page::updateOrCreate(['slug' => $page['slug']], $page);
        }
    }
}
