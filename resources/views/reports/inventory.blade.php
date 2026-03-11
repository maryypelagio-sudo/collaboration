<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Report</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #3b82f6;
        }
        .header h1 {
            font-size: 24px;
            color: #1e293b;
            margin: 0;
        }
        .header p {
            color: #64748b;
            margin: 5px 0 0;
        }
        .stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .stat-box {
            background: #f8fafc;
            padding: 15px 20px;
            border-radius: 8px;
            text-align: center;
            flex: 1;
            margin: 0 5px;
        }
        .stat-box:first-child {
            margin-left: 0;
        }
        .stat-box:last-child {
            margin-right: 0;
        }
        .stat-box .label {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .stat-box .value {
            font-size: 20px;
            font-weight: bold;
            color: #1e293b;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background: #3b82f6;
            color: white;
            padding: 12px 10px;
            text-align: left;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
        }
        td {
            padding: 12px 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        tr:nth-child(even) {
            background: #f8fafc;
        }
        tr:hover {
            background: #f1f5f9;
        }
        .status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-in-stock {
            background: #dcfce7;
            color: #166534;
        }
        .status-low-stock {
            background: #fef3c7;
            color: #92400e;
        }
        .status-out-of-stock {
            background: #fee2e2;
            color: #991b1b;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Inventory Report</h1>
        <p>Generated on {{ $generated_at }}</p>
    </div>

    <div class="stats">
        <div class="stat-box">
            <div class="label">Total Items</div>
            <div class="value">{{ $stats['total_items'] }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Categories</div>
            <div class="value">{{ $stats['total_categories'] }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Low Stock</div>
            <div class="value">{{ $stats['low_stock'] }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Min Stock</th>
                <th>Unit</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td>{{ $item->category->name ?? 'Uncategorized' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->min_stock_level }}</td>
                <td>{{ $item->unit }}</td>
                <td>
                    @if($item->quantity <= 0)
                        <span class="status status-out-of-stock">Out of Stock</span>
                    @elseif($item->quantity <= $item->min_stock_level)
                        <span class="status status-low-stock">Low Stock</span>
                    @else
                        <span class="status status-in-stock">In Stock</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Inventory Management System - Report</p>
    </div>
</body>
</html>

