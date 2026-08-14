<?php

namespace App\Support;

final class SimplePdfDocument
{
    private const PAGE_WIDTH = 595.28;
    private const PAGE_HEIGHT = 841.89;

    /** @var array<int, string> */
    private array $pages = [];
    private string $current = '';

    public function newPage(): void
    {
        if ($this->current !== '') {
            $this->pages[] = $this->current;
        }
        $this->current = '';
    }

    public function text(float $x, float $y, string $text, float $size = 10, bool $bold = false, array $rgb = [0.12, 0.16, 0.22]): void
    {
        $font = $bold ? 'F2' : 'F1';
        $this->current .= sprintf(
            "BT /%s %.2F Tf %.3F %.3F %.3F rg %.2F %.2F Td (%s) Tj ET\n",
            $font,
            $size,
            $rgb[0],
            $rgb[1],
            $rgb[2],
            $x,
            $y,
            $this->escape($text)
        );
    }

    /** @return array{lines:int,bottom:float} */
    public function wrappedText(float $x, float $y, string $text, float $width, float $size = 10, float $leading = 13, bool $bold = false, array $rgb = [0.12, 0.16, 0.22], ?int $maxLines = null): array
    {
        $lines = $this->wrap($text, $width, $size);
        if ($maxLines !== null && count($lines) > $maxLines) {
            $lines = array_slice($lines, 0, $maxLines);
            $last = array_pop($lines) ?? '';
            $lines[] = rtrim(substr($last, 0, max(0, strlen($last) - 3))).'...';
        }
        foreach ($lines as $line) {
            $this->text($x, $y, $line, $size, $bold, $rgb);
            $y -= $leading;
        }
        return ['lines' => count($lines), 'bottom' => $y];
    }

    public function line(float $x1, float $y1, float $x2, float $y2, float $width = 0.7, array $rgb = [0.85, 0.88, 0.92]): void
    {
        $this->current .= sprintf("%.3F %.3F %.3F RG %.2F w %.2F %.2F m %.2F %.2F l S\n", $rgb[0], $rgb[1], $rgb[2], $width, $x1, $y1, $x2, $y2);
    }

    public function rect(float $x, float $y, float $width, float $height, float $lineWidth = 0.7, array $rgb = [0.85, 0.88, 0.92]): void
    {
        $this->current .= sprintf("%.3F %.3F %.3F RG %.2F w %.2F %.2F %.2F %.2F re S\n", $rgb[0], $rgb[1], $rgb[2], $lineWidth, $x, $y, $width, $height);
    }

    public function fillRect(float $x, float $y, float $width, float $height, array $rgb): void
    {
        $this->current .= sprintf("%.3F %.3F %.3F rg %.2F %.2F %.2F %.2F re f\n", $rgb[0], $rgb[1], $rgb[2], $x, $y, $width, $height);
    }

    public function output(): string
    {
        if ($this->current !== '' || $this->pages === []) {
            $this->pages[] = $this->current;
            $this->current = '';
        }

        $objects = [];
        $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $pageCount = count($this->pages);
        $pageObjectNumbers = [];
        $contentObjectNumbers = [];
        $next = 5;
        for ($i = 0; $i < $pageCount; $i++) {
            $pageObjectNumbers[] = $next++;
            $contentObjectNumbers[] = $next++;
        }
        $kids = implode(' ', array_map(fn (int $n): string => $n.' 0 R', $pageObjectNumbers));
        $objects[2] = '<< /Type /Pages /Kids ['.$kids.'] /Count '.$pageCount.' >>';
        $objects[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>';
        $objects[4] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>';

        foreach ($this->pages as $index => $content) {
            $pageNo = $pageObjectNumbers[$index];
            $contentNo = $contentObjectNumbers[$index];
            $objects[$pageNo] = sprintf(
                '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 %.2F %.2F] /Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents %d 0 R >>',
                self::PAGE_WIDTH,
                self::PAGE_HEIGHT,
                $contentNo
            );
            $objects[$contentNo] = "<< /Length ".strlen($content)." >>\nstream\n".$content."endstream";
        }

        ksort($objects);
        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [0 => 0];
        foreach ($objects as $number => $body) {
            $offsets[$number] = strlen($pdf);
            $pdf .= $number." 0 obj\n".$body."\nendobj\n";
        }

        $xref = strlen($pdf);
        $max = max(array_keys($objects));
        $pdf .= "xref\n0 ".($max + 1)."\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= $max; $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i] ?? 0);
        }
        $pdf .= "trailer\n<< /Size ".($max + 1)." /Root 1 0 R >>\nstartxref\n".$xref."\n%%EOF\n";

        return $pdf;
    }

    /** @return list<string> */
    private function wrap(string $text, float $width, float $size): array
    {
        $text = trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
        if ($text === '') return [''];

        $maxChars = max(8, (int) floor($width / max(1, $size * 0.52)));
        $words = preg_split('/\s+/', $text) ?: [$text];
        $lines = [];
        $line = '';
        foreach ($words as $word) {
            if (strlen($word) > $maxChars) {
                if ($line !== '') {
                    $lines[] = $line;
                    $line = '';
                }
                while (strlen($word) > $maxChars) {
                    $lines[] = substr($word, 0, $maxChars);
                    $word = substr($word, $maxChars);
                }
                $line = $word;
                continue;
            }
            $candidate = $line === '' ? $word : $line.' '.$word;
            if (strlen($candidate) > $maxChars) {
                $lines[] = $line;
                $line = $word;
            } else {
                $line = $candidate;
            }
        }
        if ($line !== '') $lines[] = $line;
        return $lines ?: [''];
    }

    private function escape(string $text): string
    {
        $text = preg_replace('/[\r\n\t]+/', ' ', $text) ?? $text;
        $converted = @iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $text);
        if ($converted !== false) $text = $converted;
        $text = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
        return $text;
    }
}
