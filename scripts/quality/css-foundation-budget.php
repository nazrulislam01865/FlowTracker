#!/usr/bin/env php
<?php

declare(strict_types=1);

$root = realpath(__DIR__ . '/../..');
if ($root === false) {
    fwrite(STDERR, "Unable to resolve project root.\n");
    exit(2);
}

$baselinePath = $root . '/quality/css-legacy-baseline.json';

function relativePath(string $path, string $root): string
{
    return ltrim(str_replace('\\', '/', substr($path, strlen($root))), '/');
}

function cssMetrics(string $path): array
{
    $text = is_file($path) ? (string) file_get_contents($path) : '';
    preg_match_all('/#[0-9a-fA-F]{3,8}\b/', $text, $hex);

    return [
        'bytes' => is_file($path) ? (int) filesize($path) : 0,
        'important' => substr_count($text, '!important'),
        'hex_colors' => count($hex[0] ?? []),
        'sha256' => is_file($path) ? hash_file('sha256', $path) : null,
    ];
}

function stripComments(string $css): string
{
    return preg_replace('~/\*.*?\*/~s', '', $css) ?? $css;
}

function countStaticColorLiterals(string $css): int
{
    $css = stripComments($css);
    $patterns = [
        '/#[0-9a-fA-F]{3,8}\b/',
        '/\brgba?\s*\(/i',
        '/\bhsla?\s*\(/i',
        '/\boklch\s*\(/i',
        '/\boklab\s*\(/i',
    ];

    $count = 0;
    foreach ($patterns as $pattern) {
        $matches = preg_match_all($pattern, $css, $unused);
        $count += $matches === false ? 0 : $matches;
    }

    return $count;
}

if (!is_file($baselinePath)) {
    fwrite(STDERR, "Missing quality/css-legacy-baseline.json.\n");
    exit(2);
}

$baseline = json_decode((string) file_get_contents($baselinePath), true, flags: JSON_THROW_ON_ERROR);
$failures = [];
$rows = [];

foreach (($baseline['files'] ?? []) as $relative => $limits) {
    $path = $root . '/' . $relative;
    if (!is_file($path)) {
        $rows[] = [$relative, 'removed', 'PASS'];
        continue;
    }

    $current = cssMetrics($path);
    foreach (['bytes', 'important', 'hex_colors'] as $metric) {
        $limit = (int) ($limits[$metric] ?? 0);
        $value = (int) $current[$metric];
        $status = $value <= $limit ? 'PASS' : 'FAIL';
        $rows[] = ["$relative::$metric", "$value <= $limit", $status];
        if ($status === 'FAIL') {
            $failures[] = "$relative $metric increased to $value (ceiling $limit)";
        }
    }
}

$allowedPublicCss = [];
foreach (array_keys($baseline['files'] ?? []) as $relative) {
    if (str_starts_with($relative, 'public/css/')) {
        $allowedPublicCss[] = $relative;
    }
}
sort($allowedPublicCss);

$currentPublicCss = [];
foreach (glob($root . '/public/css/*.css') ?: [] as $path) {
    $currentPublicCss[] = relativePath($path, $root);
}
sort($currentPublicCss);

$newPublicCss = array_values(array_diff($currentPublicCss, $allowedPublicCss));
if ($newPublicCss !== []) {
    $failures[] = 'new public/css stylesheets are prohibited: ' . implode(', ', $newPublicCss);
}
$rows[] = ['public/css new files', (string) count($newPublicCss), $newPublicCss === [] ? 'PASS' : 'FAIL'];

$appCss = (string) file_get_contents($root . '/resources/css/app.css');
$appWithoutComments = stripComments($appCss);
preg_match_all('/@import\s+[\'"]([^\'"]+)[\'"]\s*;/', $appWithoutComments, $imports);
$expectedImports = [
    './foundation/tokens.css',
    './foundation/global.css',
    './components.css',
    './utilities.css',
    './legacy/historical-overrides.css',
];
$actualImports = $imports[1] ?? [];
if ($actualImports !== $expectedImports) {
    $failures[] = 'resources/css/app.css import order differs from the approved Phase 2 composition contract';
}
$remainder = preg_replace('/@import\s+[\'"][^\'"]+[\'"]\s*;/', '', $appWithoutComments) ?? '';
if (trim($remainder) !== '') {
    $failures[] = 'resources/css/app.css must remain composition-only';
}
$rows[] = ['app.css composition-only', trim($remainder) === '' ? 'yes' : 'no', trim($remainder) === '' ? 'PASS' : 'FAIL'];
$rows[] = ['app.css import order', $actualImports === $expectedImports ? 'approved' : 'changed', $actualImports === $expectedImports ? 'PASS' : 'FAIL'];

$tokensCss = (string) file_get_contents($root . '/resources/css/foundation/tokens.css');
preg_match_all('/(--[A-Za-z0-9_-]+)\s*:/', stripComments($tokensCss), $tokenMatches);
$declarations = $tokenMatches[1] ?? [];
$allowedLegacyAliases = [
    '--navy', '--navy2', '--bg', '--card', '--text', '--muted', '--line', '--blue', '--blue2',
    '--green', '--green2', '--amber', '--amber2', '--red', '--red2', '--purple', '--purple2', '--shadow', '--radius',
];
$invalidTokens = array_values(array_filter(
    $declarations,
    static fn (string $name): bool => !str_starts_with($name, '--ft-') && !in_array($name, $allowedLegacyAliases, true)
));
$duplicates = array_keys(array_filter(array_count_values($declarations), static fn (int $count): bool => $count > 1));
if ($invalidTokens !== []) {
    $failures[] = 'tokens.css contains unapproved custom-property names: ' . implode(', ', $invalidTokens);
}
if ($duplicates !== []) {
    $failures[] = 'tokens.css contains duplicate custom-property declarations: ' . implode(', ', $duplicates);
}
$rows[] = ['token naming', $invalidTokens === [] ? 'approved' : (string) count($invalidTokens) . ' invalid', $invalidTokens === [] ? 'PASS' : 'FAIL'];
$rows[] = ['duplicate tokens', (string) count($duplicates), $duplicates === [] ? 'PASS' : 'FAIL'];

$managedFiles = [
    'resources/css/foundation/global.css',
    'resources/css/utilities.css',
];
foreach ($managedFiles as $relative) {
    $css = (string) file_get_contents($root . '/' . $relative);
    $literalCount = countStaticColorLiterals($css);
    $importantCount = substr_count(stripComments($css), '!important');
    $rows[] = ["$relative static colors", (string) $literalCount, $literalCount === 0 ? 'PASS' : 'FAIL'];
    $rows[] = ["$relative !important", (string) $importantCount, $importantCount === 0 ? 'PASS' : 'FAIL'];
    if ($literalCount > 0) {
        $failures[] = "$relative contains $literalCount static color literal(s); use tokens.css";
    }
    if ($importantCount > 0) {
        $failures[] = "$relative contains $importantCount !important declaration(s)";
    }

    $withoutComments = stripComments($css);
    preg_match_all('/\.([A-Za-z_-][A-Za-z0-9_-]*)/', $withoutComments, $classMatches);
    $invalidClasses = array_values(array_unique(array_filter(
        $classMatches[1] ?? [],
        static fn (string $class): bool => !str_starts_with($class, 'ft-') && !str_starts_with($class, 'u-')
    )));
    if ($invalidClasses !== []) {
        $failures[] = "$relative contains non-namespaced class(es): " . implode(', ', $invalidClasses);
    }
    $rows[] = ["$relative namespace", $invalidClasses === [] ? 'approved' : implode(',', $invalidClasses), $invalidClasses === [] ? 'PASS' : 'FAIL'];
}

$historicalCss = (string) file_get_contents($root . '/resources/css/legacy/historical-overrides.css');
$historicalNoComments = stripComments($historicalCss);
$historicalColors = countStaticColorLiterals($historicalCss);
$historicalImportant = substr_count($historicalNoComments, '!important');
preg_match_all('/(^|})\s*([^@][^{]+)\{/m', $historicalNoComments, $historicalRules);
$historicalRuleCount = count($historicalRules[0] ?? []);

if (!str_contains($historicalNoComments, "@import '../flowtrack.css';")) {
    $failures[] = 'historical-overrides.css must import ../flowtrack.css';
}
if ($historicalColors > 0 || $historicalImportant > 0 || $historicalRuleCount > 1) {
    $failures[] = 'historical-overrides.css exceeded its Phase 1 compatibility allowance';
}

$rows[] = ['historical override static colors', (string) $historicalColors, $historicalColors === 0 ? 'PASS' : 'FAIL'];
$rows[] = ['historical override !important', (string) $historicalImportant, $historicalImportant === 0 ? 'PASS' : 'FAIL'];
$rows[] = ['historical override selector blocks', "$historicalRuleCount <= 1", $historicalRuleCount <= 1 ? 'PASS' : 'FAIL'];

printf("CSS foundation governance\n%-72s %-20s %s\n", 'Check', 'Value', 'Status');
foreach ($rows as [$name, $value, $status]) {
    printf("%-72s %-20s %s\n", $name, $value, $status);
}

if ($failures !== []) {
    fwrite(STDERR, "\nCSS foundation gate failed:\n - " . implode("\n - ", $failures) . "\n");
    exit(1);
}

echo "\nPASS: Phase 1 CSS foundation and legacy debt boundaries are intact.\n";
