<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * ExportService handles data export functionality for transactions and reports
 */
class ExportService
{
    /**
     * Export transactions to CSV format
     *
     * @param User $user The user whose transactions to export
     * @param array $filters Optional filters (date_from, date_to, type)
     * @return string CSV content
     */
    public function exportTransactionsToCSV(User $user, array $filters = []): string
    {
        $transactions = $this->getTransactionsForExport($user, $filters);
        
        // Create CSV content
        $csv = $this->generateCSVHeader();
        
        foreach ($transactions as $transaction) {
            $csv .= $this->formatTransactionRow($transaction);
        }
        
        return $csv;
    }

    /**
     * Export transactions to JSON format
     *
     * @param User $user The user whose transactions to export
     * @param array $filters Optional filters
     * @return string JSON content
     */
    public function exportTransactionsToJSON(User $user, array $filters = []): string
    {
        $transactions = $this->getTransactionsForExport($user, $filters);
        
        $data = $transactions->map(function ($transaction) {
            return [
                'transaction_id' => $transaction->id,
                'date' => $transaction->created_at->format('Y-m-d H:i:s'),
                'type' => $transaction->type,
                'description' => $transaction->description,
                'amount' => number_format($transaction->amount, 2),
                'status' => $transaction->status,
                'order_number' => $transaction->order?->order_number,
            ];
        });
        
        return json_encode([
            'export_date' => now()->format('Y-m-d H:i:s'),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'total_transactions' => $transactions->count(),
            'transactions' => $data,
        ], JSON_PRETTY_PRINT);
    }

    /**
     * Get transactions for export based on filters
     *
     * @param User $user
     * @param array $filters
     * @return Collection
     */
    protected function getTransactionsForExport(User $user, array $filters = []): Collection
    {
        $query = Transaction::where('user_id', $user->id)
            ->with('order')
            ->orderBy('created_at', 'desc');
        
        // Apply date filters
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
        
        // Apply type filter
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        
        return $query->get();
    }

    /**
     * Generate CSV header row
     *
     * @return string
     */
    protected function generateCSVHeader(): string
    {
        return "Transaction ID,Date,Type,Description,Amount (ETB),Status,Order Number\n";
    }

    /**
     * Format a transaction as a CSV row
     *
     * @param Transaction $transaction
     * @return string
     */
    protected function formatTransactionRow(Transaction $transaction): string
    {
        return sprintf(
            "%d,%s,%s,\"%s\",%s,%s,%s\n",
            $transaction->id,
            $transaction->created_at->format('Y-m-d H:i:s'),
            $transaction->type,
            str_replace('"', '""', $transaction->description), // Escape quotes
            number_format($transaction->amount, 2),
            $transaction->status,
            $transaction->order?->order_number ?? 'N/A'
        );
    }

    /**
     * Generate filename for export
     *
     * @param string $type Export type (csv, json)
     * @param User $user
     * @return string
     */
    public function generateExportFilename(string $type, User $user): string
    {
        $date = now()->format('Y-m-d');
        $userId = $user->id;
        
        return "transactions_{$userId}_{$date}.{$type}";
    }

    /**
     * Get MIME type for export format
     *
     * @param string $format
     * @return string
     */
    public function getMimeType(string $format): string
    {
        return match ($format) {
            'csv' => 'text/csv',
            'json' => 'application/json',
            default => 'application/octet-stream',
        };
    }
}
