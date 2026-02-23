<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\City;

class CitiesExportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $q;
    public $county_id;

    public function __construct($q = null, $county_id = null)
    {
        $this->q = $q;
        $this->county_id = $county_id;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Városok listája (PDF)');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.cities_export');
    }

    public function attachments(): array
    {
        ini_set('memory_limit', '2G');
        set_time_limit(300);

        $query = City::with(['county', 'postalCodes']);

        if (!empty($this->q)) {
            $query->where(function($subQuery) {
                $subQuery->where('name', 'LIKE', "%{$this->q}%")
                         ->orWhereHas('postalCodes', function($q2) {
                             $q2->where('code', 'LIKE', "%{$this->q}%");
                         });
            });
        }

        if (!empty($this->county_id)) {
            $query->where('county_id', $this->county_id);
        }

        $cities = $query->orderBy('name')->get();

        $pdf = Pdf::loadView('cities.pdf', [
            'cities' => $cities,
            'title' => 'Városok listája (Szűrt)',
            'date' => now()->format('Y-m-d H:i:s')
        ]);

        return [
            Attachment::fromData(fn () => $pdf->output(), 'varosok.pdf')
                ->withMime('application/pdf'),
        ];
    }
}