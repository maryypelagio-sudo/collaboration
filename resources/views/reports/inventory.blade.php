<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @page {
            size: A4;
            margin: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 10px;
            color: #1a1a2e;
            line-height: 1.4;
            background: #fff;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
        }
        
        .page {
            padding: 20px 30px;
        }
        
        /* Header Design */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
            padding-bottom: 12px;
            border-bottom: 3px solid #4361ee;
            position: relative;
        }
        
        .company-name {
            font-size: 11px;
            font-weight: 700;
            color: #4361ee;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }
        
        .header h1 {
            font-size: 20px;
            color: #1a1a2e;
            margin: 0;
            font-weight: 700;
        }
        
        .header-subtitle {
            color: #6b7280;
            margin-top: 2px;
            font-size: 9px;
        }
        
        .header-right {
            text-align: right;
        }
        
        .report-meta {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 12px 18px;
            border-radius: 8px;
            border-left: 4px solid #4361ee;
        }
        
        .report-meta .label {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }
        
        .report-meta .value {
            font-size: 13px;
            color: #1a1a2e;
            font-weight: 600;
            margin-top: 2px;
        }
        
        .report-id {
            font-size: 10px;
            color: #9ca3af;
            margin-top: 8px;
        }
        
        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 15px;
        }
        
        .stat-card {
            background: #fff;
            padding: 12px;
            border-radius: 8px;
            position: relative;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #4361ee, #3f37c9);
        }
        
        .stat-card:nth-child(2)::before {
            background: linear-gradient(90deg, #06b6d4, #0891b2);
        }
        
        .stat-card:nth-child(3)::before {
            background: linear-gradient(90deg, #f59e0b, #d97706);
        }
        
        .stat-icon {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 6px;
            font-size: 14px;
        }
        
        .stat-card:nth-child(1) .stat-icon {
            background: #eef2ff;
            color: #4361ee;
        }
        
        .stat-card:nth-child(2) .stat-icon {
            background: #ecfeff;
            color: #0891b2;
        }
        
        .stat-card:nth-child(3) .stat-icon {
            background: #fffbeb;
            color: #d97706;
        }
        
        .stat-label {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        
        .stat-value {
            font-size: 22px;
            font-weight: 700;
            color: #1a1a2e;
            line-height: 1;
        }
        
        .stat-card:nth-child(1) .stat-value { color: #4361ee; }
        .stat-card:nth-child(2) .stat-value { color: #0891b2; }
        .stat-card:nth-child(3) .stat-value { color: #d97706; }
        
        /* Table Design */
        .table-container {
            margin-top: 12px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }
        
        .table-header {
            background: linear-gradient(135deg, #1a1a2e 0%, #2d2d44 100%);
            padding: 10px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .table-header h2 {
            color: #fff;
            font-size: 11px;
            font-weight: 600;
        }
        
        .table-count {
            background: rgba(255,255,255,0.15);
            color: #fff;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }
        
        thead {
            background: #f8fafc;
        }
        
        th {
            padding: 8px 10px;
            text-align: left;
            font-weight: 600;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #4b5563;
            border-bottom: 2px solid #e5e7eb;
        }
        
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #f3f4f6;
            color: #374151;
            font-size: 9px;
        }
        
        tbody tr {
            transition: background 0.2s ease;
        }
        
        tbody tr:nth-child(even) {
            background: #fafbfc;
        }
        
        tbody tr:hover {
            background: #f0f5ff;
        }
        
        tbody tr:last-child td {
            border-bottom: none;
        }
        
        .item-name {
            font-weight: 600;
            color: #1a1a2e;
        }
        
        .category-badge {
            display: inline-block;
            padding: 4px 10px;
            background: #f3f4f6;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 500;
            color: #6b7280;
        }
        
        .quantity-cell {
            font-weight: 600;
            font-family: 'Courier New', monospace;
        }
        
        /* Status Badges */
        .status {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            margin-right: 6px;
        }
        
        .status-in-stock {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-in-stock::before {
            background: #10b981;
        }
        
        .status-low-stock {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-low-stock::before {
            background: #f59e0b;
        }
        
        .status-out-of-stock {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .status-out-of-stock::before {
            background: #ef4444;
        }
        
        /* Footer */
        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .footer-left {
            color: #6b7280;
            font-size: 9px;
        }
        
        .footer-right {
            display: flex;
            align-items: center;
            gap: 15px;
            color: #9ca3af;
            font-size: 9px;
        }
        
        .footer-brand {
            font-weight: 600;
            color: #4361ee;
        }
        
        /* Summary Section */
        .summary {
            margin-top: 15px;
            padding: 12px;
            background: linear-gradient(135deg, #f8fafc 0%, #f0f5ff 100%);
            border-radius: 8px;
            border: 1px solid #e0e7ff;
        }
        
        .summary h3 {
            font-size: 10px;
            color: #1a1a2e;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }
        
        .summary-item {
            text-align: center;
            padding: 6px;
        }
        
        .summary-item .label {
            font-size: 8px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .summary-item .value {
            font-size: 14px;
            font-weight: 700;
            margin-top: 2px;
        }
        
        .summary-item:nth-child(1) .value { color: #4361ee; }
        .summary-item:nth-child(2) .value { color: #10b981; }
        .summary-item:nth-child(3) .value { color: #f59e0b; }
        .summary-item:nth-child(4) .value { color: #ef4444; }
    </style>
</head>
<body>
    <div class="page">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <div class="company-name">Inventory Management</div>
                <h1>Inventory Report</h1>
                <p class="header-subtitle">Complete overview of current stock levels and inventory status</p>
            </div>
            <div class="header-right">
                <div class="report-meta">
                    <div class="label">Generated On</div>
                    <div class="value">{{ $generated_at }}</div>
                </div>
                <div class="report-id">Report ID: INV-{{ date('Ymd') }}</div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon">📦</div>
                <div class="stat-label">Total Items</div>
                <div class="stat-value">{{ $stats['total_items'] }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🏷️</div>
                <div class="stat-label">Categories</div>
                <div class="stat-value">{{ $stats['total_categories'] }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">⚠️</div>
                <div class="stat-label">Low Stock Items</div>
                <div class="stat-value">{{ $stats['low_stock'] }}</div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="table-container">
            <div class="table-header">
                <h2>Inventory Details</h2>
                <span class="table-count">{{ count($items) }} Items</span>
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
                        <td class="item-name">{{ $item->name }}</td>
                        <td><span class="category-badge">{{ $item->category->name ?? 'Uncategorized' }}</span></td>
                        <td class="quantity-cell">{{ $item->quantity }}</td>
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
        </div>

        <!-- Summary Section -->
        <div class="summary">
            <h3>Inventory Summary</h3>
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="label">Total Stock</div>
                    <div class="value">{{ $summary['total_stock'] }}</div>
                </div>
                <div class="summary-item">
                    <div class="label">Healthy Items</div>
                    <div class="value">{{ $summary['healthy_items'] }}</div>
                </div>
                <div class="summary-item">
                    <div class="label">Need Attention</div>
                    <div class="value">{{ $summary['need_attention'] }}</div>
                </div>
                <div class="summary-item">
                    <div class="label">Out of Stock</div>
                    <div class="value">{{ $summary['out_of_stock'] }}</div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-left">
                © {{ date('Y') }} Inventory Management System. All rights reserved.
            </div>
            <div class="footer-right">
                <span>Page 1 of 1</span>
                <span class="footer-brand">Generated by IMS</span>
            </div>
        </div>
    </div>
</body>
</html>

