<?php

namespace App\DTOs\Orders;

final readonly class OrderCreateData
{
    public function __construct(public array $attributes)
    {
    }

    public static function fromLivewire(array $data, bool $draft): self
    {
        $first = collect($data['jobItems'])->first();

        return new self([
            'order_number' => $data['referenceNumber'],
            'is_repeat_order' => (bool) $data['isRepeatedOrder'],
            'repeat_order_number' => $data['isRepeatedOrder'] ? trim((string) $data['repeatedOrderNumber']) : null,
            'title' => self::generateTitle($data['referenceNumber'], $data['jobItems']),
            'product' => $first['product'] ?? null,
            'category' => $first['category'] ?? null,
            'quantity' => collect($data['jobItems'])->sum('quantity'),
            'items' => $data['jobItems'],
            'priority' => 'Medium',
            'production_urgency_ids' => [],
            'shipment_urgency_ids' => array_values(array_map('intval', $data['shipmentUrgencyIds'] ?? [])),
            'client_id' => $data['clientId'],
            'workflow_id' => $data['workflowId'],
            'workflow_phase_id' => $data['workflowPhaseId'],
            'owner_id' => $data['ownerId'],
            'coordinator_id' => $data['coordinatorId'] ?: $data['ownerId'],
            'delivery_date' => $data['deliveryDate'] ?: null,
            'estimated_delivery_date' => $data['estimatedDeliveryDate'] ?: null,
            'description' => $data['description'],
            'shipping_address' => blank($data['shippingAddress'] ?? null) ? null : trim((string) $data['shippingAddress']),
            'shipping_contact_type' => blank($data['shippingContactType'] ?? null) ? null : trim((string) $data['shippingContactType']),
            'shipping_contact_name' => blank($data['shippingContactName'] ?? null) ? null : trim((string) $data['shippingContactName']),
            'shipping_phone_country_code' => blank($data['shippingPhoneCountryCode'] ?? null) ? null : trim((string) $data['shippingPhoneCountryCode']),
            'shipping_phone' => blank($data['shippingPhone'] ?? null) ? null : trim((string) $data['shippingPhone']),
            'shipping_postal_code' => blank($data['shippingPostalCode'] ?? null) ? null : trim((string) $data['shippingPostalCode']),
            'shipping_source_address_id' => $data['shippingSourceAddressId'] ?? null,
            'draft' => $draft,
        ]);
    }

    /**
     * Create the human-readable Order title without duplicating user input.
     *
     * Examples:
     *   FO-333119 – Premium Cotton T-Shirt
     *   FO-333119 – Premium Cotton T-Shirt + 2 more
     *
     * FlowTrack's generated job_number/Order Code is deliberately independent
     * from this title and continues to be assigned by LegacyJobService.
     *
     * @param array<int,array<string,mixed>> $items
     */
    public static function generateTitle(string $clientReference, array $items): string
    {
        $reference = trim($clientReference);
        $products = collect($items)
            ->map(fn (array $item): string => trim((string) ($item['product'] ?? '')))
            ->filter(fn (string $name): bool => $name !== '')
            ->values();

        $firstProduct = (string) ($products->first() ?? 'Product');
        $remaining = max(0, $products->count() - 1);
        $suffix = $remaining > 0 ? ' + '.$remaining.' more' : '';
        $prefix = $reference.' – ';
        $title = $prefix.$firstProduct.$suffix;

        // flow_jobs.title is VARCHAR(255). Normal product/reference names fit
        // comfortably, but keep pathological long master-data values safe while
        // preserving the client reference and the "+ N more" indicator where possible.
        if (mb_strlen($title) <= 255) {
            return $title;
        }

        $availableForProduct = 255 - mb_strlen($prefix) - mb_strlen($suffix);
        if ($availableForProduct > 1) {
            return $prefix.mb_substr($firstProduct, 0, $availableForProduct - 1).'…'.$suffix;
        }

        return mb_substr($title, 0, 255);
    }

    public function toArray(): array
    {
        return $this->attributes;
    }
}
