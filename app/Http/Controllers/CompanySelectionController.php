<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanySelectionController extends Controller
{
    /**
     * Show company selection page.
     */
    public function select()
    {
        $companies = Company::orderBy('name')->get();
        return view('companies.select', compact('companies'));
    }

    /**
     * Store selected company in session.
     */
    public function store(Request $request)
    {
        $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
        ]);

        session(['selected_company_id' => $request->company_id]);

        return redirect()->route('dashboard');
    }

    /**
     * Switch company.
     */
    public function switch()
    {
        session()->forget('selected_company_id');
        return redirect()->route('companies.select');
    }
}
