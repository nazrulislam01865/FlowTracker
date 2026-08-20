#!/usr/bin/env php
<?php

declare(strict_types=1);

$root = realpath(__DIR__ . '/../..');
if ($root === false) {
    fwrite(STDERR, "Unable to resolve project root.\n");
    exit(2);
}

$baselinePath = $root . '/quality/architecture-baseline.json';
$command = $argv[1] ?? '--check';

function normalizePath(string $path, string $root): string
{
    return ltrim(str_replace('\\', '/', substr($path, strlen($root))), '/');
}

function collectFiles(string $directory, string $suffix): array
{
    if (!is_dir($directory)) {
        return [];
    }

    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && str_ends_with($file->getFilename(), $suffix)) {
            $files[] = $file->getPathname();
        }
    }

    sort($files);
    return $files;
}

function readTextFiles(array $files): string
{
    $text = '';
    foreach ($files as $file) {
        $contents = file_get_contents($file);
        if ($contents !== false) {
            $text .= "\n" . $contents;
        }
    }
    return $text;
}

function regexCount(string $pattern, string $text): int
{
    $result = preg_match_all($pattern, $text, $matches);
    return $result === false ? 0 : $result;
}

function matchingLineCount(array $files, string $pattern): int
{
    $count = 0;
    foreach ($files as $file) {
        $lines = file($file, FILE_IGNORE_NEW_LINES);
        if ($lines === false) continue;
        foreach ($lines as $line) {
            if (preg_match($pattern, $line) === 1) $count++;
        }
    }
    return $count;
}

function lineCount(string $file): int
{
    $contents = file_get_contents($file);
    if ($contents === false || $contents === '') {
        return 0;
    }
    return substr_count($contents, "\n") + (str_ends_with($contents, "\n") ? 0 : 1);
}

function metrics(string $root): array
{
    $bladeFiles = collectFiles($root . '/resources/views', '.blade.php');
    $livewireFiles = collectFiles($root . '/app/Livewire', '.php');
    $serviceFiles = collectFiles($root . '/app/Services', '.php');
    $testFiles = collectFiles($root . '/tests', '.php');
    $modelFiles = collectFiles($root . '/app/Models', '.php');
    $appPhpFiles = collectFiles($root . '/app', '.php');

    $bladeText = readTextFiles($bladeFiles);
    $modelText = readTextFiles($modelFiles);
    $appText = readTextFiles($appPhpFiles);

    $allCssFiles = collectFiles($root . '/resources/css', '.css');

    // Phase 3 relocated previously external/Blade-owned compatibility CSS into
    // resources/css so Vite can own it. Those files are not new architectural
    // debt and must not distort the Phase 0 ceilings simply because their path
    // changed. Their bytes/colors/!important ceilings are enforced separately
    // by scripts/quality/phase3-migration.php.
    $phase3RelocatedLegacy = static function (string $file) use ($root): bool {
        $relative = normalizePath($file, $root);

        if (str_starts_with($relative, 'resources/css/legacy/compatibility/')) {
            return true;
        }

        return in_array($relative, [
            'resources/css/migration/components/summary-card.css',
            'resources/css/migration/components/date-range-filter.css',
            'resources/css/migration/components/export-period-modal.css',
            'resources/css/modules/orders/list.css',
            'resources/css/modules/work/all-tasks.css',
        ], true);
    };

    $architectureCssFiles = array_values(array_filter(
        $allCssFiles,
        static fn (string $file): bool => ! $phase3RelocatedLegacy($file)
    ));
    $allCssText = readTextFiles($architectureCssFiles);
    $canonicalCssFiles = array_values(array_filter(
        $architectureCssFiles,
        static fn (string $file): bool => !str_contains(str_replace('\\', '/', $file), '/resources/css/generated/')
    ));
    $canonicalCssText = readTextFiles($canonicalCssFiles);

    // Phase 1 establishes tokens.css as the one allowed owner of static design
    // values. Hard-coded color debt excludes that authoritative token source
    // while continuing to count legacy/component/page stylesheets.
    $hardcodedColorDebtFiles = array_values(array_filter(
        $canonicalCssFiles,
        static fn (string $file): bool => normalizePath($file, $root) !== 'resources/css/foundation/tokens.css'
    ));
    $hardcodedColorDebtText = readTextFiles($hardcodedColorDebtFiles);

    $flowtrackPath = $root . '/resources/css/flowtrack.css';
    $flowtrackText = is_file($flowtrackPath) ? (string) file_get_contents($flowtrackPath) : '';

    preg_match_all('/#[0-9a-fA-F]{3,8}\b/', $flowtrackText, $flowtrackHexMatches);
    $uniqueHex = array_unique(array_map('strtolower', $flowtrackHexMatches[0] ?? []));

    $giantPaths = [
        'app/Livewire/Jobs/Index.php',
        'app/Livewire/MasterData/Index.php',
        'app/Livewire/Inquiries/Index.php',
        'app/Services/InquiryService.php',
        'app/Services/DashboardService.php',
        'app/Services/JobService.php',
    ];
    $giantFiles = [];
    foreach ($giantPaths as $relative) {
        $path = $root . '/' . $relative;
        $giantFiles[$relative] = is_file($path) ? lineCount($path) : 0;
    }

    return [
        'snapshot' => [
            'blade_files' => count($bladeFiles),
            'livewire_files' => count($livewireFiles),
            'service_files' => count($serviceFiles),
            'test_files' => count($testFiles),
        ],
        'budgets' => [
            'giant_files' => $giantFiles,
            // These debt counts intentionally match the roadmap's original line-based static scan.
            'blade_php_blocks' => matchingLineCount($bladeFiles, '/@php\b/'),
            'blade_app_calls' => matchingLineCount($bladeFiles, '/\bapp\s*\(/'),
            'blade_auth_calls' => matchingLineCount($bladeFiles, '/\bauth\s*\(/'),
            'blade_style_attributes' => matchingLineCount($bladeFiles, '/\bstyle\s*=/'),
            'blade_style_blocks' => matchingLineCount($bladeFiles, '/<style\b/i'),
            'blade_hardcoded_hex_colors' => regexCount('/#[0-9a-fA-F]{3,8}\b/', $bladeText),
            'css_important_all' => regexCount('/!important\b/', $allCssText),
            'css_important_canonical' => regexCount('/!important\b/', $canonicalCssText),
            'css_hardcoded_hex_canonical' => regexCount('/#[0-9a-fA-F]{3,8}\b/', $hardcodedColorDebtText),
            'flowtrack_css_bytes' => is_file($flowtrackPath) ? filesize($flowtrackPath) : 0,
            'flowtrack_css_lines' => is_file($flowtrackPath) ? lineCount($flowtrackPath) : 0,
            'flowtrack_css_important' => regexCount('/!important\b/', $flowtrackText),
            'flowtrack_css_hex_occurrences' => count($flowtrackHexMatches[0] ?? []),
            'flowtrack_css_unique_hex' => count($uniqueHex),
            'models_guarded_empty' => regexCount('/protected\s+\$guarded\s*=\s*\[\s*\]/', $modelText),
            'app_get_calls' => matchingLineCount($appPhpFiles, '/->get\s*\(/'),
        ],
    ];
}

function flattenBudgets(array $budgets, string $prefix = ''): array
{
    $flat = [];
    foreach ($budgets as $key => $value) {
        $name = $prefix === '' ? (string) $key : $prefix . '.' . $key;
        if (is_array($value)) {
            $flat += flattenBudgets($value, $name);
        } else {
            $flat[$name] = (int) $value;
        }
    }
    return $flat;
}

$current = metrics($root);

if ($command === '--print') {
    echo json_encode($current, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
    exit(0);
}

if ($command === '--write-baseline') {
    $payload = [
        'schema' => 1,
        'generated_at' => gmdate('c'),
        'policy' => 'non_increasing',
        'note' => 'Phase 0 debt ceiling. Refactor work should reduce these metrics; new debt must not exceed them.',
        'snapshot' => $current['snapshot'],
        'budgets' => $current['budgets'],
    ];
    file_put_contents($baselinePath, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL);
    echo "Wrote architecture baseline: " . normalizePath($baselinePath, $root) . PHP_EOL;
    exit(0);
}

if ($command !== '--check') {
    fwrite(STDERR, "Usage: php scripts/quality/architecture-budget.php [--check|--print|--write-baseline]\n");
    exit(2);
}

if (!is_file($baselinePath)) {
    fwrite(STDERR, "Missing quality/architecture-baseline.json. Run --write-baseline once on the approved Phase 0 snapshot.\n");
    exit(2);
}

$baseline = json_decode((string) file_get_contents($baselinePath), true, flags: JSON_THROW_ON_ERROR);
$expected = flattenBudgets($baseline['budgets'] ?? []);
$actual = flattenBudgets($current['budgets']);
$failures = [];

foreach ($expected as $metric => $limit) {
    $value = $actual[$metric] ?? null;
    if ($value === null) {
        $failures[] = "$metric: metric missing from current scan";
        continue;
    }
    if ($value > $limit) {
        $failures[] = "$metric: $value > baseline $limit";
    }
}

printf("Architecture budget check\n%-48s %12s %12s %10s\n", 'Metric', 'Current', 'Baseline', 'Status');
foreach ($expected as $metric => $limit) {
    $value = $actual[$metric] ?? -1;
    $status = $value <= $limit ? 'PASS' : 'FAIL';
    printf("%-48s %12d %12d %10s\n", $metric, $value, $limit, $status);
}

if ($failures !== []) {
    fwrite(STDERR, "\nArchitecture debt increased:\n - " . implode("\n - ", $failures) . "\n");
    exit(1);
}

echo "\nPASS: architecture debt did not increase.\n";
