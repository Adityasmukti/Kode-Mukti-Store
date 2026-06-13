<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadRequest;
use App\Models\Lead;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LeadMagnetController extends Controller
{
    public function store(StoreLeadRequest $request)
    {
        $email = $request->validated('email');

        $lead = Lead::updateOrCreate(
            ['email' => $email],
            [
                'download_token' => $this->generateUniqueToken(),
                'first_opted_at' => now(),
                'last_opted_at' => now(),
            ]
        );

        if ($lead->wasRecentlyCreated === false) {
            $lead->updateQuietly([
                'last_opted_at' => now(),
            ]);
        }

        Log::info('Umami Event: Lead Captured', [
            'email' => $lead->email,
            'download_token' => $lead->download_token,
        ]);

        return redirect()->route('lead-magnet.show', $lead->download_token);
    }

    public function downloadPage(string $downloadToken)
    {
        $lead = Lead::where('download_token', $downloadToken)->firstOrFail();

        return view('lead-magnet.show', [
            'lead' => $lead,
            'isSpecial' => $this->isSpecialEdition(),
        ]);
    }

    public function download(string $downloadToken)
    {
        $lead = Lead::where('download_token', $downloadToken)->firstOrFail();

        $leadMagnetPath = $this->isSpecialEdition()
            ? 'lead-magnets/50-prompt-pemasaran-gratis-100.pdf'
            : config('products.lead_magnet_path');

        abort_unless(\Illuminate\Support\Facades\Storage::disk('local')->exists($leadMagnetPath), 404);

        return \Illuminate\Support\Facades\Storage::disk('local')->download(
            $leadMagnetPath,
            '50-prompt-pemasaran-gratis.pdf'
        );
    }

    private function isSpecialEdition(): bool
    {
        return Lead::count() <= 100;
    }

    private function generateUniqueToken(): string
    {
        do {
            $token = Str::random(48);
        } while (Lead::where('download_token', $token)->exists());

        return $token;
    }
}
