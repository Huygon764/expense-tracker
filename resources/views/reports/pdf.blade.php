<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Báo cáo chi tiêu</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; padding: 20px; }
        h1 { font-size: 18px; margin-bottom: 8px; }
        .meta { color: #555; margin-bottom: 16px; }
        .total { font-size: 14px; font-weight: bold; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background: #f5f5f5; font-weight: bold; }
        h2 { font-size: 14px; margin-top: 16px; margin-bottom: 8px; }
    </style>
</head>
<body>
    <h1>Báo cáo chi tiêu</h1>
    <p class="meta">Khoảng: {{ $date_from }} — {{ $date_to }}</p>
    <p class="total">Tổng chi: {{ number_format($total, 0, '.', ',') }}</p>

    <h2>Theo danh mục</h2>
    <table>
        <thead>
            <tr>
                <th>Danh mục</th>
                <th>Tổng</th>
            </tr>
        </thead>
        <tbody>
            @forelse($by_category as $row)
                <tr>
                    <td>{{ $row['category_name'] }}</td>
                    <td>{{ number_format($row['total'], 0, '.', ',') }}</td>
                </tr>
            @empty
                <tr><td colspan="2">Không có dữ liệu</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Chi tiêu</h2>
    <table>
        <thead>
            <tr>
                <th>Ngày</th>
                <th>Danh mục</th>
                <th>Số tiền</th>
                <th>Ghi chú</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $expense)
                <tr>
                    <td>{{ $expense->date->format('Y-m-d') }}</td>
                    <td>{{ $expense->category?->name ?? '—' }}</td>
                    <td>{{ number_format($expense->amount, 0, '.', ',') }}</td>
                    <td>{{ Str::limit($expense->note ?? '—', 40) }}</td>
                </tr>
            @empty
                <tr><td colspan="4">Không có chi tiêu trong khoảng này</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
