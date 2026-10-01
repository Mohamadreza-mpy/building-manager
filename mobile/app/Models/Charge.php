<?php

namespace App\Models;

final readonly class Charge
{
    public function __construct(
        public int $id,
        public int $buildingId,
        public int $apartmentId,
        public string $title,
        public string $month,
        public string $amount,
        public string $status,
        public ?string $paidAt,
        public ?string $paymentReceiptUrl,
        public ?string $receiptSubmittedAt,
        public ?array $apartment,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            buildingId: (int) $data['building_id'],
            apartmentId: (int) $data['apartment_id'],
            title: (string) $data['title'],
            month: (string) $data['month'],
            amount: (string) $data['amount'],
            status: (string) $data['status'],
            paidAt: isset($data['paid_at']) ? (string) $data['paid_at'] : null,
            paymentReceiptUrl: isset($data['payment_receipt_url']) ? (string) $data['payment_receipt_url'] : null,
            receiptSubmittedAt: isset($data['receipt_submitted_at']) ? (string) $data['receipt_submitted_at'] : null,
            apartment: is_array($data['apartment'] ?? null) ? $data['apartment'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'building_id' => $this->buildingId,
            'apartment_id' => $this->apartmentId,
            'title' => $this->title,
            'month' => $this->month,
            'amount' => $this->amount,
            'status' => $this->status,
            'status_label' => $this->statusLabel(),
            'paid_at' => $this->paidAt,
            'payment_receipt_url' => $this->paymentReceiptUrl,
            'receipt_submitted_at' => $this->receiptSubmittedAt,
            'apartment' => $this->apartment,
        ];
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'paid' => 'پرداخت‌شده',
            'overdue' => 'سررسید گذشته',
            default => 'در انتظار پرداخت',
        };
    }
}
