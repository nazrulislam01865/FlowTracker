<?php

namespace App\Services;

use App\Models\Invoice;
use App\Support\SimplePdfDocument;
use Illuminate\Support\Facades\Storage;

class InvoicePdfService
{
    public function generate(Invoice $invoice): Invoice
    {
        $invoice->loadMissing([
            'items',
            'creator:id,name',
            'job.client',
            'job.owner:id,name',
        ]);

        $pdf = $this->render($invoice);
        $filename = $this->filename($invoice);
        $path = 'invoices/'.$invoice->flow_job_id.'/generated/'.$filename;
        $disk = Storage::disk('local');
        $oldPath = (string) ($invoice->pdf_path ?? '');

        $stored = $disk->put($path, $pdf);
        throw_if(!$stored, \RuntimeException::class, 'The generated invoice PDF could not be stored. Please try again.');

        try {
            $invoice->update([
                'pdf_path' => $path,
                'pdf_name' => $filename,
                'pdf_generated_at' => now(),
            ]);
        } catch (\Throwable $exception) {
            $disk->delete($path);
            throw $exception;
        }

        if ($oldPath !== '' && $oldPath !== $path) {
            $disk->delete($oldPath);
        }

        return $invoice->refresh();
    }

    public function ensure(Invoice $invoice): Invoice
    {
        $path = (string) ($invoice->pdf_path ?? '');
        if ($path !== '' && Storage::disk('local')->exists($path)) {
            return $invoice;
        }

        return $this->generate($invoice);
    }

    public function filename(Invoice $invoice): string
    {
        $base = preg_replace('/[^A-Za-z0-9._-]+/', '-', (string) $invoice->invoice_number) ?: 'invoice-'.$invoice->id;
        return trim($base, '-').'.pdf';
    }

    private function render(Invoice $invoice): string
    {
        $job = $invoice->job;
        $client = $job?->client;
        $branding = app(BrandingService::class)->current();
        $workspaceName = trim((string) ($branding['name'] ?? 'FlowTrack')) ?: 'FlowTrack';
        $currency = strtoupper((string) ($invoice->currency ?: 'USD'));
        $money = fn (float|int|string|null $amount): string => $this->money((float) ($amount ?? 0), $currency);
        $doc = new SimplePdfDocument();

        $page = 1;
        $y = 0.0;
        $startPage = function (bool $continued = false) use ($doc, $workspaceName, $invoice, &$page, &$y): void {
            if ($page > 1) {
                $doc->newPage();
            }
            $doc->fillRect(0, 806, 595.28, 35, [0.055, 0.145, 0.285]);
            $doc->text(42, 818, $this->plain($workspaceName), 15, true, [1, 1, 1]);
            $doc->text(435, 818, $continued ? 'INVOICE - CONTINUED' : 'INVOICE', 10, true, [1, 1, 1]);
            $doc->text(42, 788, $this->plain($invoice->invoice_number), 18, true, [0.08, 0.14, 0.24]);
            $doc->text(490, 790, strtoupper($this->plain((string) $invoice->status)), 8, true, [0.08, 0.38, 0.82]);
            $doc->line(42, 778, 553, 778, 0.8, [0.82, 0.86, 0.91]);
            $y = 758;
            $page++;
        };

        $startPage(false);

        // Invoice metadata.
        $doc->text(42, $y, 'Issue date', 8, true, [0.38, 0.44, 0.54]);
        $doc->text(145, $y, 'Due date', 8, true, [0.38, 0.44, 0.54]);
        $doc->text(248, $y, 'Invoice type', 8, true, [0.38, 0.44, 0.54]);
        $doc->text(404, $y, 'Currency', 8, true, [0.38, 0.44, 0.54]);
        $doc->text(42, $y - 15, $invoice->issue_date?->format('M j, Y') ?: '-', 10, false);
        $doc->text(145, $y - 15, $invoice->due_date?->format('M j, Y') ?: '-', 10, false);
        $doc->text(248, $y - 15, $this->plain(str_replace(' invoice', '', (string) $invoice->type)), 10, false);
        $doc->text(404, $y - 15, $currency, 10, false);
        $y -= 45;

        // Bill-to and order boxes.
        $boxTop = $y;
        $doc->fillRect(42, $boxTop - 78, 246, 78, [0.97, 0.98, 0.995]);
        $doc->rect(42, $boxTop - 78, 246, 78, 0.6, [0.84, 0.88, 0.93]);
        $doc->text(54, $boxTop - 17, 'BILL TO', 8, true, [0.08, 0.38, 0.82]);
        $doc->text(54, $boxTop - 34, $this->plain((string) ($client?->name ?: 'Client')), 11, true);
        if ($invoice->billing_contact_name) {
            $doc->text(54, $boxTop - 50, $this->plain((string) $invoice->billing_contact_name), 9, false, [0.28, 0.34, 0.43]);
        }
        if ($invoice->billing_contact_email) {
            $doc->text(54, $boxTop - 64, $this->plain((string) $invoice->billing_contact_email), 8, false, [0.38, 0.44, 0.54]);
        }

        $doc->fillRect(307, $boxTop - 78, 246, 78, [0.97, 0.98, 0.995]);
        $doc->rect(307, $boxTop - 78, 246, 78, 0.6, [0.84, 0.88, 0.93]);
        $doc->text(319, $boxTop - 17, 'ORDER', 8, true, [0.08, 0.38, 0.82]);
        $doc->text(319, $boxTop - 34, $this->plain($job?->displayOrderNumber() ?: '-'), 10, true);
        $doc->wrappedText(319, $boxTop - 49, $this->plain((string) ($job?->title ?: 'Order')), 220, 8, 11, false, [0.28, 0.34, 0.43], 2);
        if ($job?->order_number) {
            $doc->text(319, $boxTop - 70, 'Reference: '.$this->plain((string) $job->order_number), 8, false, [0.38, 0.44, 0.54]);
        }
        $y = $boxTop - 102;

        if ($invoice->purchase_order_reference) {
            $doc->text(42, $y, 'Purchase order reference', 8, true, [0.38, 0.44, 0.54]);
            $doc->text(180, $y, $this->plain((string) $invoice->purchase_order_reference), 9, false);
            $y -= 24;
        }

        $drawItemsHeader = function () use ($doc, &$y): void {
            $doc->fillRect(42, $y - 22, 511, 22, [0.055, 0.145, 0.285]);
            $doc->text(50, $y - 14, 'DESCRIPTION', 8, true, [1, 1, 1]);
            $doc->text(337, $y - 14, 'QTY', 8, true, [1, 1, 1]);
            $doc->text(400, $y - 14, 'UNIT PRICE', 8, true, [1, 1, 1]);
            $doc->text(480, $y - 14, 'AMOUNT', 8, true, [1, 1, 1]);
            $y -= 22;
        };

        $drawItemsHeader();
        foreach ($invoice->items as $item) {
            $description = $this->plain((string) $item->description);
            $descriptionLines = $this->wrapPlain($description, 42);
            $rowHeight = max(31, 12 + count($descriptionLines) * 11);
            if ($y - $rowHeight < 120) {
                $startPage(true);
                $drawItemsHeader();
            }

            $doc->fillRect(42, $y - $rowHeight, 511, $rowHeight, [1, 1, 1]);
            $doc->line(42, $y - $rowHeight, 553, $y - $rowHeight, 0.6, [0.88, 0.90, 0.93]);
            $lineY = $y - 18;
            foreach ($descriptionLines as $line) {
                $doc->text(50, $lineY, $line, 9, false);
                $lineY -= 11;
            }
            $doc->text(337, $y - 19, $this->quantity((float) $item->quantity), 9, false);
            $doc->text(400, $y - 19, $money($item->unit_price), 9, false);
            $doc->text(480, $y - 19, $money($item->amount), 9, true);
            $y -= $rowHeight;
        }

        if ($y < 230) {
            $startPage(true);
        }

        $totalsX = 345.0;
        $labelX = 360.0;
        $valueX = 486.0;
        $doc->fillRect($totalsX, $y - 118, 208, 118, [0.97, 0.98, 0.995]);
        $doc->rect($totalsX, $y - 118, 208, 118, 0.6, [0.84, 0.88, 0.93]);
        $doc->text($labelX, $y - 20, 'Subtotal', 9, false, [0.35, 0.41, 0.49]);
        $doc->text($valueX, $y - 20, $money($invoice->subtotal), 9, true);
        $doc->text($labelX, $y - 42, 'Tax '.rtrim(rtrim(number_format((float) $invoice->tax_rate, 2), '0'), '.').'%', 9, false, [0.35, 0.41, 0.49]);
        $doc->text($valueX, $y - 42, $money($invoice->tax_amount), 9, true);
        if ((float) $invoice->previously_invoiced > 0) {
            $doc->text($labelX, $y - 64, 'Previously invoiced', 9, false, [0.35, 0.41, 0.49]);
            $doc->text($valueX, $y - 64, '-'.$money($invoice->previously_invoiced), 9, true);
        }
        $doc->line($labelX, $y - 79, 540, $y - 79, 0.7, [0.78, 0.82, 0.88]);
        $doc->text($labelX, $y - 101, 'Invoice total', 10, true);
        $doc->text($valueX, $y - 101, $money($invoice->total), 12, true, [0.08, 0.38, 0.82]);

        $notesTop = $y - 12;
        if ($invoice->notes) {
            $doc->text(42, $notesTop, 'NOTES / PAYMENT INSTRUCTIONS', 8, true, [0.38, 0.44, 0.54]);
            $doc->wrappedText(42, $notesTop - 17, $this->plain((string) $invoice->notes), 270, 9, 12, false, [0.28, 0.34, 0.43], 7);
        }

        $footerY = 42.0;
        $doc->line(42, $footerY + 14, 553, $footerY + 14, 0.6, [0.86, 0.89, 0.93]);
        $doc->text(42, $footerY, 'Generated by '.$this->plain($workspaceName), 7, false, [0.48, 0.53, 0.61]);
        $doc->text(420, $footerY, 'Invoice '.$this->plain((string) $invoice->invoice_number), 7, false, [0.48, 0.53, 0.61]);

        return $doc->output();
    }

    private function money(float $amount, string $currency): string
    {
        $prefix = match ($currency) {
            'USD' => '$',
            'EUR' => 'EUR ',
            'GBP' => 'GBP ',
            'CNY', 'RMB' => 'CNY ',
            default => $currency.' ',
        };
        return $prefix.number_format($amount, 2);
    }

    private function quantity(float $quantity): string
    {
        if (abs($quantity - round($quantity)) < 0.00001) {
            return number_format((int) round($quantity));
        }
        return rtrim(rtrim(number_format($quantity, 2), '0'), '.');
    }

    private function plain(string $value): string
    {
        $value = strip_tags(html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $value = trim(preg_replace('/\s+/u', ' ', $value) ?? $value);
        return $value;
    }

    /** @return list<string> */
    private function wrapPlain(string $text, int $maxChars): array
    {
        $text = trim($text);
        if ($text === '') return [''];
        $words = preg_split('/\s+/', $text) ?: [$text];
        $lines = [];
        $line = '';
        foreach ($words as $word) {
            $candidate = $line === '' ? $word : $line.' '.$word;
            if (strlen($candidate) > $maxChars && $line !== '') {
                $lines[] = $line;
                $line = $word;
            } else {
                $line = $candidate;
            }
        }
        if ($line !== '') $lines[] = $line;
        return $lines ?: [''];
    }
}
