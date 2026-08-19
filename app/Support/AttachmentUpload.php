<?php

namespace App\Support;

use Closure;
use Illuminate\Support\Facades\Validator;

final class AttachmentUpload
{
    /**
     * EPS and ESP are accepted by original filename extension because some
     * systems report them as application/postscript or application/octet-stream
     * instead of a stable MIME-specific extension.
     */
    public const VECTOR = ['eps', 'esp'];

    public const DOCUMENTS = [
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'zip', 'txt', 'csv',
    ];

    public const DOCUMENTS_WITH_AI = [
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'zip', 'txt', 'csv', 'ai',
    ];

    public const ORDER_REQUIRED = [
        'pdf', 'docx', 'xlsx', 'jpg', 'jpeg', 'png', 'zip',
    ];

    public const FINANCE = [
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'csv', 'txt',
    ];

    // Product certificate/template fields previously relied on Livewire's
    // global temporary-upload whitelist, so preserve that effective set here.
    public const PRODUCT_SUPPORTING = [
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'webp', 'ico', 'zip', 'txt', 'csv', 'ai',
    ];

    public static function requiredRules(array $extensions = self::DOCUMENTS, int $maxKilobytes = 20480): array
    {
        return ['required', 'file', 'max:'.$maxKilobytes, self::typeRule($extensions)];
    }

    public static function nullableRules(array $extensions = self::DOCUMENTS, int $maxKilobytes = 20480): array
    {
        return ['nullable', 'file', 'max:'.$maxKilobytes, self::typeRule($extensions)];
    }

    public static function itemRules(array $extensions = self::DOCUMENTS, int $maxKilobytes = 20480): array
    {
        return ['file', 'max:'.$maxKilobytes, self::typeRule($extensions)];
    }

    public static function extensions(array $extensions = self::DOCUMENTS): array
    {
        $normalized = array_map(
            static fn (mixed $extension): string => strtolower(trim((string) $extension)),
            array_merge($extensions, self::VECTOR),
        );

        return array_values(array_unique(array_filter($normalized)));
    }

    public static function accept(array $extensions = self::DOCUMENTS): string
    {
        return implode(',', array_map(static fn (string $extension): string => '.'.$extension, self::extensions($extensions)));
    }

    public static function validationMessage(array $extensions = self::DOCUMENTS): string
    {
        $labels = [];
        foreach (self::extensions($extensions) as $extension) {
            $label = match ($extension) {
                'jpeg' => 'JPG',
                default => strtoupper($extension),
            };
            if (!in_array($label, $labels, true)) {
                $labels[] = $label;
            }
        }

        if (count($labels) === 1) {
            return 'Unsupported file type. Use '.$labels[0].'.';
        }

        $last = array_pop($labels);

        return 'Unsupported file type. Use '.implode(', ', $labels).' or '.$last.'.';
    }

    private static function typeRule(array $extensions): Closure
    {
        $standardExtensions = array_values(array_diff(self::extensions($extensions), self::VECTOR));
        $allowedExtensions = self::extensions($extensions);
        $message = self::validationMessage($extensions);

        return static function (string $attribute, mixed $value, Closure $fail) use ($standardExtensions, $allowedExtensions, $message): void {
            if (!is_object($value) || !method_exists($value, 'getClientOriginalExtension')) {
                return; // The normal `file` rule reports invalid upload values.
            }

            $extension = strtolower(trim((string) $value->getClientOriginalExtension()));
            if (!in_array($extension, $allowedExtensions, true)) {
                $fail($message);
                return;
            }

            // EPS / ESP are deliberately extension-based exceptions. EPS is
            // commonly detected as generic PostScript and ESP commonly arrives
            // as application/octet-stream, so MIME-to-extension validation is
            // not reliable for these two requested attachment formats.
            if (in_array($extension, self::VECTOR, true)) {
                return;
            }

            // Preserve the existing Laravel MIME validation behavior for every
            // format that FlowTrack already supported before EPS / ESP support.
            $validator = Validator::make(
                ['upload' => $value],
                ['upload' => ['mimes:'.implode(',', $standardExtensions)]],
            );

            if ($validator->fails()) {
                $fail($message);
            }
        };
    }
}
