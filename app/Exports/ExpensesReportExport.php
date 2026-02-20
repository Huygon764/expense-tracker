<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExpensesReportExport implements FromArray, WithHeadings
{
    public function __construct(
        private array $data
    ) {}

    public function headings(): array
    {
        return ['Ngày', 'Danh mục', 'Số tiền', 'Ghi chú'];
    }

    public function array(): array
    {
        $rows = [];
        foreach ($this->data['expenses'] as $expense) {
            $rows[] = [
                $expense->date->format('Y-m-d'),
                $expense->category?->name ?? '—',
                (float) $expense->amount,
                $expense->note ?? '—',
            ];
        }
        $rows[] = ['', '', (float) $this->data['total'], 'Tổng chi'];
        return $rows;
    }
}
