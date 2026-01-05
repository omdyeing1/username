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
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 30px;
            background: white;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #4F46E5;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #4F46E5;
        }
        
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            margin-top: 10px;
        }
        
        .party-box {
            background: #f8f9fa;
            padding: 15px 20px;
            border-radius: 8px;
            border-left: 4px solid #4F46E5;
        }
        
        .info-label {
            font-size: 11px;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 3px;
        }
        
        .challan-badges span {
            background: #e8f4f8;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            margin-right: 5px;
            display: inline-block;
            margin-bottom: 5px;
        }
        
        .items-table th {
            background: #4F46E5;
            color: white;
            font-size: 12px;
            text-transform: uppercase;
        }
        
        .summary-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
        }
        
        .final-amount {
            font-size: 28px;
            font-weight: bold;
            color: #4F46E5;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            width: 200px;
            text-align: center;
            padding-top: 5px;
            margin: 0 auto;
        }
        
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <button class="btn btn-primary print-btn no-print" onclick="window.print()">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 5px;">
            <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
            <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
        </svg>
        Print Invoice
    </button>
    
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="company-name">{{ $company->name ?? 'YOUR COMPANY NAME' }}</div>
            <div class="text-muted">
                {{ $company->address ?? 'Company Address' }}
                @if($company && $company->gst_number)
                    | GST: {{ $company->gst_number }}
                @endif
                @if($company && $company->state_code)
                    | State: {{ $company->state_code }}
                @endif
                @if($company && $company->mobile_numbers)
                    | Phone: {{ $company->mobile_numbers }}
                @endif
            </div>
            <div class="invoice-title">TAX INVOICE</div>
        </div>
        
        <!-- Invoice Info -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="info-label">Bill To</div>
                <div class="party-box">
                    <h5 class="mb-2">{{ $invoice->party->name }}</h5>
                    <p class="mb-1">{{ $invoice->party->address }}</p>
                    <p class="mb-1"><strong>Contact:</strong> {{ $invoice->party->contact_number }}</p>
                    @if($invoice->party->gst_number)
                    <p class="mb-0"><strong>GSTIN:</strong> {{ $invoice->party->gst_number }}</p>
                    @endif
                </div>
            </div>
            <div class="col-md-6 text-end">
                <div class="info-label">Invoice Number</div>
                <h4 class="text-primary mb-3">{{ $invoice->invoice_number }}</h4>
                
                <div class="info-label">Invoice Date</div>
                <p class="mb-3">{{ $invoice->invoice_date->format('d M Y') }}</p>
                
                <div class="info-label">Generated On</div>
                <p class="mb-0">{{ now()->format('d M Y, h:i A') }}</p>
            </div>
        </div>
        
        <!-- Challans -->
        <div class="mb-4">
            <div class="info-label mb-2">Challans Included</div>
            <div class="challan-badges">
                @foreach($invoice->challans as $challan)
                    <span>{{ $challan->challan_number }} ({{ $challan->challan_date->format('d/m/Y') }})</span>
                @endforeach
            </div>
        </div>
        
        <!-- Items Table -->
        <table class="table items-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Description</th>
                    <th class="text-end">Qty</th>
                    <th>Unit</th>
                    <th class="text-end">Rate (₹)</th>
                    <th class="text-end">Amount (₹)</th>
                </tr>
            </thead>
            <tbody>
                @php $itemNum = 1; @endphp
                @foreach($invoice->challans as $challan)
                    @foreach($challan->items as $item)
                    <tr>
                        <td>{{ $itemNum++ }}</td>
                        <td>{{ $item->description }}</td>
                        <td class="text-end">{{ number_format($item->quantity, 3) }}</td>
                        <td>{{ $item->unit }}</td>
                        <td class="text-end">{{ number_format($item->rate, 2) }}</td>
                        <td class="text-end">{{ number_format($item->amount, 2) }}</td>
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
        
        <!-- Summary -->
        <div class="row">
            <div class="col-md-6"></div>
            <div class="col-md-6">
                <div class="summary-section">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td>Subtotal:</td>
                            <td class="text-end">₹ {{ number_format($invoice->subtotal, 2) }}</td>
                        </tr>
                        @if($invoice->gst_amount > 0)
                        <tr>
                            <td>GST ({{ $invoice->gst_percent }}%):</td>
                            <td class="text-end text-success">+ ₹ {{ number_format($invoice->gst_amount, 2) }}</td>
                        </tr>
                        @endif
                        @if($invoice->tds_amount > 0)
                        <tr>
                            <td>TDS ({{ $invoice->tds_percent }}%):</td>
                            <td class="text-end text-danger">- ₹ {{ number_format($invoice->tds_amount, 2) }}</td>
                        </tr>
                        @endif
                        @if($invoice->discount_amount > 0)
                        <tr>
                            <td>Discount @if($invoice->discount_type == 'percentage')({{ $invoice->discount_value }}%)@endif:</td>
                            <td class="text-end text-danger">- ₹ {{ number_format($invoice->discount_amount, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="border-top">
                            <td class="fw-bold">Total Payable:</td>
                            <td class="text-end final-amount">₹ {{ number_format($invoice->final_amount, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        @if($invoice->notes)
        <div class="alert alert-warning mt-4">
            <strong>Notes:</strong> {{ $invoice->notes }}
        </div>
        @endif
        
        <!-- Signature -->
        <div class="row mt-5 pt-4">
            <div class="col-6 text-center">
                <div class="signature-line">Receiver's Signature</div>
            </div>
            <div class="col-6 text-center">
                <div class="signature-line">Authorized Signature</div>
            </div>
        </div>
        
        <div class="text-center text-muted mt-4" style="font-size: 11px;">
            This is a computer-generated invoice. | Thank you for your business!
        </div>
    </div>
</body>
</html>
