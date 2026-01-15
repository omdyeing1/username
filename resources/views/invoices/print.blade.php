<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }} - Print</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            .container { max-width: 100% !important; }
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: white;
            font-size: 11px;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 30px;
            background: white;
        }
        
        .invoice-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            text-decoration: underline;
        }
        
        .header-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        
        .header-left {
            width: 70%;
        }
        
        .header-right {
            width: 30%;
            text-align: right;
        }
        
        .company-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .company-details {
            font-size: 10px;
            line-height: 1.5;
        }
        
        .invoice-info-table {
            width: 100%;
            margin-bottom: 15px;
        }
        
        .party-section {
            width: 60%;
        }
        
        .invoice-section {
            width: 40%;
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
            width: 60%;
            margin-left: auto;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        .summary-table td {
            padding: 4px 8px;
            font-size: 10px;
        }
        
        .summary-table .label {
            text-align: left;
            width: 50%;
        }
        
        .summary-table .value {
            text-align: right;
            width: 50%;
        }
        
        .total-row {
            font-weight: bold;
            font-size: 11px;
            border-top: 1px solid #000;
            padding-top: 4px;
        }
        
        .amount-words {
            margin-top: 8px;
            font-weight: bold;
            font-size: 10px;
        }
        
        .bank-details {
            margin-top: 15px;
            font-size: 10px;
            line-height: 1.6;
        }
        
        .terms {
            margin-top: 15px;
            font-size: 9px;
            line-height: 1.5;
        }
        
        .terms ol {
            margin-left: 20px;
            padding-left: 5px;
        }
        
        .terms li {
            margin-bottom: 4px;
        }
        
        .signature {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Invoice Title -->
        <div class="invoice-title">TAX INVOICE</div>
        
        <!-- Header with Company Details -->
        <div class="header-row">
            <div class="header-left">
                <div class="company-name">{{ $company->name ?? 'YOUR COMPANY NAME' }}</div>
                <div class="company-details">
                    {{ $company->address ?? 'Address Line 1, City, State - Pincode' }}<br>
                    GSTI No :{{ $company->gst_number ?? 'XXXXXXXXXXXXXXX' }}<br>
                    State Code: {{ $company->state_code ?? 'XX-XX' }}
                </div>
            </div>
            <div class="header-right">
                <div class="company-details">
                    Mo: {{ $company->mobile_numbers ?? 'XXXXXXXXXX - XXXXXXXXXX' }}
                </div>
            </div>
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
                    @if($invoice->party->gst_number && strlen($invoice->party->gst_number) >= 4)
                    <div>State Code: {{ substr($invoice->party->gst_number, 0, 2) }}-{{ substr($invoice->party->gst_number, 2, 2) }}</div>
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
                    <th style="width: 30%;">PARTICULARS</th>
                    <th style="width: 8%;">HSN</th>
                    <th style="width: 10%;">Taka</th>
                    <th style="width: 10%;">Mtrs</th>
                    <th style="width: 12%;">RATE</th>
                    <th style="width: 12%;">AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalTaka = 0;
                    $totalMtrs = 0;
                    $totalAmount = 0;
                @endphp
                @foreach($invoice->challans as $challan)
                    @foreach($challan->items as $item)
                        @php
                            $totalTaka += $item->quantity;
                            $totalMtrs += $item->quantity;
                            $totalAmount += $item->amount;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $challan->challan_number }}</td>
                            <td class="text-center">{{ $challan->challan_date->format('d/m/y') }}</td>
                            <td>{{ $item->description }}</td>
                            <td class="text-center"></td>
                            <td class="text-right">{{ number_format($item->quantity, 2) }}</td>
                            <td class="text-right">{{ number_format($item->quantity, 2) }}</td>
                            <td class="text-right">{{ number_format($item->rate, 2) }}</td>
                            <td class="text-right">{{ number_format($item->amount, 2) }}</td>
                        </tr>
                    @endforeach
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
                
                // Number to words function
                function numberToWords($number) {
                    $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 
                             'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
                    $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
                    
                    if ($number < 20) {
                        return $ones[$number];
                    } elseif ($number < 100) {
                        return $tens[floor($number / 10)] . ($number % 10 ? ' ' . $ones[$number % 10] : '');
                    } elseif ($number < 1000) {
                        return $ones[floor($number / 100)] . ' Hundred' . ($number % 100 ? ' ' . numberToWords($number % 100) : '');
                    } elseif ($number < 100000) {
                        return numberToWords(floor($number / 1000)) . ' Thousand' . ($number % 1000 ? ' ' . numberToWords($number % 1000) : '');
                    } elseif ($number < 10000000) {
                        return numberToWords(floor($number / 100000)) . ' Lakh' . ($number % 100000 ? ' ' . numberToWords($number % 100000) : '');
                    } else {
                        return numberToWords(floor($number / 10000000)) . ' Crore' . ($number % 10000000 ? ' ' . numberToWords($number % 10000000) : '');
                    }
                }
                
                $amountInWords = numberToWords((int)$invoice->final_amount);
                $paise = (int)(($invoice->final_amount - (int)$invoice->final_amount) * 100);
                if ($paise > 0) {
                    $amountInWords .= ' and ' . numberToWords($paise) . ' Paise';
                }
                $amountInWords .= ' Only';
            @endphp
            
            <table class="summary-table">
                <tr>
                    <td class="label">CGST</td>
                    <td class="value">{{ number_format($cgstPercent, 2) }}%</td>
                    <td class="value">{{ number_format($cgstAmount, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">SGST</td>
                    <td class="value">{{ number_format($sgstPercent, 2) }}%</td>
                    <td class="value">{{ number_format($sgstAmount, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">Rounding</td>
                    <td class="value">{{ number_format(0, 2) }}%</td>
                    <td class="value">{{ number_format($rounding, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td class="label">Total Amount:</td>
                    <td></td>
                    <td class="value">{{ number_format($invoice->final_amount, 2) }}</td>
                </tr>
            </table>
            
            <div class="amount-words">
                Rupees {{ ucwords(strtolower($amountInWords)) }}
            </div>
        </div>
        
        <!-- Bank Details -->
        @if($company && ($company->bank_name || $company->ifsc_code || $company->account_number))
        <div class="bank-details">
            <strong>Bank Name:</strong> {{ $company->bank_name ?? 'N/A' }}<br>
            <strong>IFSC Code:</strong> {{ $company->ifsc_code ?? 'N/A' }}<br>
            <strong>A/c No:</strong> {{ $company->account_number ?? 'N/A' }}
        </div>
        @endif
        
        <!-- Terms and Conditions -->
        <div class="terms">
            @if($company && $company->terms_conditions)
                {!! nl2br(e($company->terms_conditions)) !!}
            @else
                <ol>
                    <li>Goods sold will not be taken back.</li>
                    <li>Payment will be accepted only by A/c payee's draft/cheque.</li>
                    <li>Interest at 2.0% per month charged on account not paid within due course.</li>
                    <li>Subject to SURAT Jurisdiction. E. & O.E.</li>
                    <li>No Dyeing guarantee.</li>
                </ol>
            @endif
        </div>
        
        <!-- Signature -->
        <div class="signature">
            FOR {{ strtoupper($company->name ?? 'COMPANY') }}
        </div>
    </div>
</body>
</html>
