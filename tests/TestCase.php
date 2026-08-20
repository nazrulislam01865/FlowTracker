<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function compatibilityCss(string $filename): string
    {
        $path = resource_path('css/legacy/compatibility/'.$filename);

        $this->assertFileExists($path, 'Missing managed compatibility CSS source: '.$filename);

        return (string) file_get_contents($path);
    }

    protected function assertLayoutLoadsViteCss(string $entry, string $layout): void
    {
        $this->assertStringContainsString("@vite('".$entry."')", $layout);
    }

    /**
     * Cache-Control directive order is not semantically significant and may be
     * normalized by Symfony. Assert the policy rather than an exact string.
     */
    protected function assertCacheControlDirectives($response, array $directives): void
    {
        $header = strtolower((string) $response->headers->get('Cache-Control'));
        $actual = collect(explode(',', $header))
            ->map(fn ($directive) => trim($directive))
            ->filter()
            ->values();

        foreach ($directives as $directive) {
            $this->assertTrue(
                $actual->contains(strtolower(trim((string) $directive))),
                'Missing Cache-Control directive ['.$directive.'] in ['.$header.'].'
            );
        }
    }
}
