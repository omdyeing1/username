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
            font-size: 11px;
            line-height: 1.3;
            color: #000;
        }
        
        .container {
            padding: 15px;
        }
        
        .header {
            margin-bottom: 15px;
        }
        
        .company-header {
            text-align: center;
            margin-bottom: 10px;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .company-details {
            font-size: 10px;
            line-height: 1.4;
        }
        
        .invoice-title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin-top: 8px;
            margin-bottom: 15px;
        }
        
        .invoice-info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .invoice-info-table td {
            padding: 5px;
            vertical-align: top;
            font-size: 10px;
        }
        
        .party-section {
            width: 50%;
        }
        
        .invoice-section {
            width: 50%;
            text-align: right;
        }
        
        .party-label {
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .party-name {
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .items-table th {
            background: #e0e0e0;
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: center;
            font-size: 10px;
            font-weight: bold;
        }
        
        .items-table td {
            border: 1px solid #000;
            padding: 5px 4px;
            font-size: 10px;
        }
        
        .items-table .text-right {
            text-align: right;
        }
        
        .items-table .text-center {
            text-align: center;
        }
        
        .summary-section {
            margin-top: 10px;
            margin-bottom: 15px;
        }
        
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        .summary-table td {
            padding: 4px 8px;
            font-size: 10px;
        }
        
        .summary-table .label {
            text-align: right;
            padding-right: 15px;
        }
        
        .summary-table .value {
            text-align: right;
            font-weight: bold;
        }
        
        .total-row {
            font-weight: bold;
            font-size: 12px;
        }
        
        .amount-words {
            margin-top: 8px;
            font-weight: bold;
            font-size: 10px;
        }
        
        .bank-details {
            margin-top: 15px;
            font-size: 10px;
        }
        
        .bank-details table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .bank-details td {
            padding: 3px 5px;
            font-size: 10px;
        }
        
        .terms-conditions {
            margin-top: 15px;
            font-size: 9px;
            line-height: 1.4;
        }
        
        .terms-conditions ol {
            margin-left: 20px;
            padding-left: 5px;
        }
        
        .terms-conditions li {
            margin-bottom: 3px;
        }
        
        .signature {
            margin-top: 40px;
            text-align: right;
            font-size: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-header">
                <div class="company-name">{{ $company->name ?? 'YOUR COMPANY NAME' }}</div>
                <div class="company-details">
                    {{ $company->address ?? 'Company Address' }}
                    @if($company && $company->gst_number)
                        | GSTI No: {{ $company->gst_number }}
                    @endif
                    @if($company && $company->state_code)
                        | State Code: {{ $company->state_code }}
                    @endif
                    @if($company && $company->mobile_numbers)
                        | Mo: {{ $company->mobile_numbers }}
                    @endif
                </div>
            </div>
            <div class="invoice-title">TAX INVOICE</div>
        </div>
        
        <!-- Invoice and Party Info -->
        <table class="invoice-info-table">
            <tr>
                <td class="party-section">
                    <div class="party-label">M/s.:</div>
                    <div class="party-name">{{ $invoice->party->name }}</div>
                    <div>{{ $invoice->party->address }}</div>
                    @if($invoice->party->gst_number)
                    <div>GSTI No. {{ $invoice->party->gst_number }}</div>
                    @endif
                </td>
                <td class="invoice-section">
                    <div><strong>INV NO.</strong> {{ $invoice->invoice_number }}</div>
                    <div><strong>Date:</strong> {{ $invoice->invoice_date->format('d/m/Y') }}</div>
                    <div><strong>Due Day:</strong> 0</div>
                    <div><strong>Due Date:</strong> {{ $invoice->invoice_date->format('d/m/Y') }}</div>
                </td>
            </tr>
        </table>
        
        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 8%;">Ch. No</th>
                    <th style="width: 10%;">Ch. Date</th>
                    <th style="width: 25%;">PARTICULARS</th>
                    <th style="width: 8%;">HSN</th>
                    <th style="width: 10%;">Taka</th>
                    <th style="width: 10%;">Mtrs</th>
                    <th style="width: 12%;">RATE</th>
                    <th style="width: 17%;">AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalTaka = 0;
                    $totalMtrs = 0;
                    $totalAmount = 0;
                @endphp
                @foreach($invoice->challans as $challan)
                    @php
                        $challanTaka = $challan->items->sum('quantity');
                        $challanMtrs = $challan->items->sum('quantity');
                        $avgRate = $challan->items->count() > 0 ? $challan->subtotal / $challanTaka : 0;
                        $totalTaka += $challanTaka;
                        $totalMtrs += $challanMtrs;
                        $totalAmount += $challan->subtotal;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $challan->challan_number }}</td>
                        <td class="text-center">{{ $challan->challan_date->format('d/m/y') }}</td>
                        <td>{{ $challan->items->first()->description ?? 'SINGLE DYEING' }}</td>
                        <td class="text-center"></td>
                        <td class="text-right">{{ number_format($challanTaka, 2) }}</td>
                        <td class="text-right">{{ number_format($challanMtrs, 2) }}</td>
                        <td class="text-right">{{ number_format($avgRate, 2) }}</td>
                        <td class="text-right">{{ number_format($challan->subtotal, 2) }}</td>
                    </tr>
                @endforeach
                <tr style="font-weight: bold;">
                    <td colspan="4" class="text-right">Total</td>
                    <td class="text-right">{{ number_format($totalTaka, 2) }}</td>
                    <td class="text-right">{{ number_format($totalMtrs, 2) }}</td>
                    <td></td>
                    <td class="text-right">{{ number_format($totalAmount, 2) }}</td>
                </tr>
            </tbody>
        </table>
        
        <!-- Summary -->
        <div class="summary-section">
            @php
                $discountedSubtotal = $invoice->subtotal - $invoice->discount_amount;
                $cgstPercent = $invoice->gst_percent / 2;
                $sgstPercent = $invoice->gst_percent / 2;
                $cgstAmount = round($discountedSubtotal * ($cgstPercent / 100), 2);
                $sgstAmount = round($discountedSubtotal * ($sgstPercent / 100), 2);
                $rounding = round($invoice->final_amount - ($discountedSubtotal + $invoice->gst_amount - $invoice->tds_amount), 2);
            @endphp
            
            <table class="summary-table">
                <tr>
                    <td class="label">Subtotal:</td>
                    <td class="value">{{ number_format($invoice->subtotal, 2) }}</td>
                </tr>
                @if($invoice->discount_amount > 0)
                <tr>
                    <td class="label">Discount:</td>
                    <td class="value">- {{ number_format($invoice->discount_amount, 2) }}</td>
                </tr>
                @endif
                @if($invoice->gst_amount > 0)
                <tr>
                    <td class="label">CGST {{ number_format($cgstPercent, 2) }}%:</td>
                    <td class="value">{{ number_format($cgstAmount, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">SGST {{ number_format($sgstPercent, 2) }}%:</td>
                    <td class="value">{{ number_format($sgstAmount, 2) }}</td>
                </tr>
                @endif
                @if($invoice->tds_amount > 0)
                <tr>
                    <td class="label">TDS {{ number_format($invoice->tds_percent, 2) }}%:</td>
                    <td class="value">- {{ number_format($invoice->tds_amount, 2) }}</td>
                </tr>
                @endif
                @if(abs($rounding) > 0.01)
                <tr>
                    <td class="label">Rounding:</td>
                    <td class="value">{{ number_format($rounding, 2) }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td class="label">Total Amount:</td>
                    <td class="value">{{ number_format($invoice->final_amount, 2) }}</td>
                </tr>
            </table>
            
            <div class="amount-words">
                Total Amount in words: <strong>Rupees {{ number_format($invoice->final_amount, 2, '.', ',') }} Only</strong>
            </div>
        </div>
        
        <!-- Bank Details -->
        @if($company && ($company->bank_name || $company->ifsc_code || $company->account_number))
        <div class="bank-details">
            <table>
                <tr>
                    <td><strong>Bank Name:</strong> {{ $company->bank_name ?? '-' }}</td>
                    <td><strong>IFSC Code:</strong> {{ $company->ifsc_code ?? '-' }}</td>
                    <td><strong>A/c No:</strong> {{ $company->account_number ?? '-' }}</td>
                </tr>
            </table>
        </div>
        @endif
        
        <!-- Terms and Conditions -->
        @if($company && $company->terms_conditions)
        <div class="terms-conditions">
            <strong>Terms and Conditions:</strong>
            <ol>
                @foreach(explode("\n", $company->terms_conditions) as $term)
                    @if(trim($term))
                        <li>{{ trim($term) }}</li>
                    @endif
                @endforeach
            </ol>
        </div>
        @endif
        
        <!-- Signature -->
        <div class="signature">
            FOR {{ strtoupper($company->name ?? 'COMPANY') }}
        </div>
    </div>
</body>
</html>
