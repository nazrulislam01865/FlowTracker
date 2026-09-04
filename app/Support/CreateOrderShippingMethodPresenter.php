<?php

namespace App\Support;

use Illuminate\Support\Collection;

/**
 * Presentation-only mapping for the Create Order shipping-method prototype.
 *
 * Shipment methods remain Master Data records. Standard Express uses the
 * existing Shipment Urgency records for its Normal/Urgent/Super Urgent level,
 * while Sea/Air remain ordinary shipment methods. This keeps the UI vocabulary
 * separate from persistence and avoids duplicating shipping labels in Blade.
 */
final class CreateOrderShippingMethodPresenter
{
    public static function methodKind(mixed $method): string
    {
        $code = strtoupper(trim((string) data_get($method, 'code', '')));
        $name = strtolower(trim((string) data_get($method, 'name', '')));

        if ($code === 'SEA' || str_contains($name, 'sea')) return 'sea';
        if ($code === 'AIR' || str_contains($name, 'air')) return 'air';
        if ($code === 'EXP' || str_contains($name, 'express') || str_contains($name, 'courier')) return 'express';

        return 'other';
    }

    public static function methodLabel(mixed $method): string
    {
        return match (self::methodKind($method)) {
            'sea' => 'Sea Shipping',
            'air' => 'Air Shipping',
            'express' => 'Standard Express Shipping',
            default => trim((string) data_get($method, 'name', '')) ?: 'Shipping method',
        };
    }

    public static function methodEstimate(mixed $method): string
    {
        return match (self::methodKind($method)) {
            'sea' => 'About 1 month',
            'air' => 'About 10–15 days',
            default => '',
        };
    }

    public static function urgencyKind(mixed $urgency): string
    {
        $name = strtolower(trim((string) data_get($urgency, 'name', '')));

        if (str_contains($name, 'super')) return 'super-urgent';
        if (str_contains($name, 'urgent')) return 'urgent';
        if (str_contains($name, 'normal')) return 'normal';

        return 'other';
    }

    public static function urgencyLabel(mixed $urgency): string
    {
        return match (self::urgencyKind($urgency)) {
            'normal' => 'Normal',
            'urgent' => 'Urgent',
            'super-urgent' => 'Super Urgent',
            default => trim((string) data_get($urgency, 'name', '')) ?: 'Express',
        };
    }

    public static function urgencyEstimate(mixed $urgency): string
    {
        return match (self::urgencyKind($urgency)) {
            'normal' => 'About 7 days',
            'urgent' => 'About 3 days',
            'super-urgent' => 'About 1–2 days',
            default => '',
        };
    }

    public static function directMethods(Collection $methods): Collection
    {
        return $methods
            ->filter(fn ($method) => self::methodKind($method) !== 'express')
            ->sortBy(fn ($method) => match (self::methodKind($method)) {
                'sea' => 10,
                'air' => 20,
                default => 30 + (int) data_get($method, 'sort_order', 0),
            })
            ->values();
    }

    public static function expressMethod(Collection $methods): mixed
    {
        return $methods->first(fn ($method) => self::methodKind($method) === 'express');
    }

    /**
     * Normal is intentionally virtual: an empty Shipment Urgency array remains
     * the established persisted meaning of normal service and keeps reporting
     * semantics backward compatible.
     */
    public static function expressUrgencies(Collection $urgencies): Collection
    {
        $canonical = collect([
            ['id' => null, 'name' => 'Normal', 'kind' => 'normal', 'estimate' => 'About 7 days'],
        ]);

        $real = $urgencies
            ->reject(fn ($urgency) => self::urgencyKind($urgency) === 'normal')
            ->sortBy(fn ($urgency) => match (self::urgencyKind($urgency)) {
                'urgent' => 10,
                'super-urgent' => 20,
                default => 30 + (int) data_get($urgency, 'sort_order', 0),
            })
            ->map(fn ($urgency) => [
                'id' => (int) data_get($urgency, 'id'),
                'name' => self::urgencyLabel($urgency),
                'kind' => self::urgencyKind($urgency),
                'estimate' => self::urgencyEstimate($urgency),
            ]);

        return $canonical->concat($real)->values();
    }

    public static function selectedCard(
        Collection $methods,
        Collection $urgencies,
        array $selectedMethodIds,
        array $selectedUrgencyIds,
    ): ?array {
        $selectedId = collect($selectedMethodIds)
            ->map(fn ($id) => (int) $id)
            ->first(fn (int $id) => $id > 0);

        if (!$selectedId) return null;

        $method = $methods->first(fn ($option) => (int) data_get($option, 'id') === $selectedId);
        if (!$method) return null;

        $kind = self::methodKind($method);
        if ($kind === 'express') {
            $urgencyId = collect($selectedUrgencyIds)
                ->map(fn ($id) => (int) $id)
                ->first(fn (int $id) => $id > 0);
            $urgency = $urgencyId
                ? $urgencies->first(fn ($option) => (int) data_get($option, 'id') === $urgencyId)
                : null;

            return [
                'method_id' => $selectedId,
                'urgency_id' => $urgencyId,
                'kind' => 'express',
                'title' => 'Standard Express Shipping — '.($urgency ? self::urgencyLabel($urgency) : 'Normal'),
                'estimate' => $urgency ? self::urgencyEstimate($urgency) : 'About 7 days',
            ];
        }

        return [
            'method_id' => $selectedId,
            'urgency_id' => null,
            'kind' => $kind,
            'title' => self::methodLabel($method),
            'estimate' => self::methodEstimate($method),
        ];
    }

    /**
     * Backward-compatible wrapper for any callers left open across deployment.
     * New Create Order renders use selectedCard() because selection is singular.
     *
     * @return array<int,array<string,mixed>>
     */
    public static function selectedCards(
        Collection $methods,
        Collection $urgencies,
        array $selectedMethodIds,
        array $selectedUrgencyIds,
    ): array {
        $card = self::selectedCard($methods, $urgencies, $selectedMethodIds, $selectedUrgencyIds);

        return $card ? [$card] : [];
    }
}
