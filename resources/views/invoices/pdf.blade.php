<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        
        .container {
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4F46E5;
            padding-bottom: 20px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #4F46E5;
            margin-bottom: 5px;
        }
        
        .company-tagline {
            font-size: 11px;
            color: #666;
        }
        
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            margin-top: 10px;
        }
        
        .invoice-info {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        
        .invoice-info-left,
        .invoice-info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .invoice-info-right {
            text-align: right;
        }
        
        .label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        
        .value {
            font-size: 13px;
            margin-bottom: 10px;
        }
        
        .party-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 25px;
        }
        
        .party-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .challans-summary {
            background: #e8f4f8;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .challans-summary strong {
            color: #4F46E5;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th {
            background: #4F46E5;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
        }
        
        td {
            padding: 10px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .summary-table {
            width: 350px;
            float: right;
            margin-top: 20px;
        }
        
        .summary-table td {
            padding: 8px 12px;
        }
        
        .summary-table .label-cell {
            background: #f0f0f0;
            font-weight: 500;
        }
        
        .summary-table .total-row {
            background: #4F46E5 !important;
            color: white;
        }
        
        .summary-table .total-row td {
            font-size: 16px;
            font-weight: bold;
            padding: 12px;
        }
        
        .positive {
            color: #10B981;
        }
        
        .negative {
            color: #EF4444;
        }
        
        .final-amount {
            font-size: 24px;
            font-weight: bold;
        }
        
        .footer {
            clear: both;
            margin-top: 80px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }
        
        .signature-section {
            display: table;
            width: 100%;
            margin-top: 50px;
        }
        
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            width: 200px;
            margin: 0 auto;
            padding-top: 5px;
        }
        
        .notes {
            margin-top: 30px;
            padding: 15px;
            background: #fff8e1;
            border-radius: 5px;
        }
        
        .notes-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-name">YOUR COMPANY NAME</div>
            <div class="company-tagline">Address Line 1, City, State - Pincode | Phone: +91 XXXXXXXXXX | GST: XXXXX</div>
            <div class="invoice-title">TAX INVOICE</div>
        </div>
        
        <!-- Invoice Info -->
        <div class="invoice-info">
            <div class="invoice-info-left">
                <div class="label">Bill To</div>
                <div class="party-box">
                    <div class="party-name">{{ $invoice->party->name }}</div>
                    <div>{{ $invoice->party->address }}</div>
                    <div style="margin-top: 5px;">
                        <strong>Contact:</strong> {{ $invoice->party->contact_number }}
                    </div>
                    @if($invoice->party->gst_number)
                    <div><strong>GSTIN:</strong> {{ $invoice->party->gst_number }}</div>
                    @endif
                </div>
            </div>
            <div class="invoice-info-right">
                <div class="label">Invoice Number</div>
                <div class="value" style="font-size: 18px; font-weight: bold; color: #4F46E5;">{{ $invoice->invoice_number }}</div>
                
                <div class="label">Invoice Date</div>
                <div class="value">{{ $invoice->invoice_date->format('d M Y') }}</div>
                
                <div class="label">Generated On</div>
                <div class="value">{{ now()->format('d M Y, h:i A') }}</div>
            </div>
        </div>
        
        <!-- Challans Summary -->
        <div class="challans-summary">
            <strong>Challans Included:</strong>
            @foreach($invoice->challans as $index => $challan)
                {{ $challan->challan_number }} ({{ $challan->challan_date->format('d/m/Y') }}){{ $index < $invoice->challans->count() - 1 ? ', ' : '' }}
            @endforeach
        </div>
        
        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 40%;">Description</th>
                    <th class="text-right" style="width: 12%;">Quantity</th>
                    <th class="text-center" style="width: 10%;">Unit</th>
                    <th class="text-right" style="width: 15%;">Rate (₹)</th>
                    <th class="text-right" style="width: 18%;">Amount (₹)</th>
                </tr>
            </thead>
            <tbody>
                @php $itemNum = 1; @endphp
                @foreach($invoice->challans as $challan)
                    @foreach($challan->items as $item)
                    <tr>
                        <td>{{ $itemNum++ }}</td>
                        <td>{{ $item->description }}</td>
                        <td class="text-right">{{ number_format($item->quantity, 3) }}</td>
                        <td class="text-center">{{ $item->unit }}</td>
                        <td class="text-right">{{ number_format($item->rate, 2) }}</td>
                        <td class="text-right">{{ number_format($item->amount, 2) }}</td>
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
        
        <!-- Summary -->
        <div class="clearfix">
            <table class="summary-table">
                <tr>
                    <td class="label-cell">Subtotal</td>
                    <td class="text-right">₹ {{ number_format($invoice->subtotal, 2) }}</td>
                </tr>
                @if($invoice->gst_amount > 0)
                <tr>
                    <td class="label-cell">GST ({{ $invoice->gst_percent }}%)</td>
                    <td class="text-right positive">+ ₹ {{ number_format($invoice->gst_amount, 2) }}</td>
                </tr>
                @endif
                @if($invoice->tds_amount > 0)
                <tr>
                    <td class="label-cell">TDS ({{ $invoice->tds_percent }}%)</td>
                    <td class="text-right negative">- ₹ {{ number_format($invoice->tds_amount, 2) }}</td>
                </tr>
                @endif
                @if($invoice->discount_amount > 0)
                <tr>
                    <td class="label-cell">
                        Discount
                        @if($invoice->discount_type == 'percentage')
                            ({{ $invoice->discount_value }}%)
                        @endif
                    </td>
                    <td class="text-right negative">- ₹ {{ number_format($invoice->discount_amount, 2) }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td>TOTAL PAYABLE</td>
                    <td class="text-right final-amount">₹ {{ number_format($invoice->final_amount, 2) }}</td>
                </tr>
            </table>
        </div>
        
        @if($invoice->notes)
        <div class="notes">
            <div class="notes-title">Notes:</div>
            <div>{{ $invoice->notes }}</div>
        </div>
        @endif
        
        <!-- Footer -->
        <div class="footer">
            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-line">Receiver's Signature</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line">Authorized Signature</div>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 30px; font-size: 10px; color: #666;">
                This is a computer-generated invoice. | Thank you for your business!
            </div>
        </div>
    </div>
</body>
</html>
