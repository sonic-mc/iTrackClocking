<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
        ]);

        Setting::updateOrCreate(
            ['key' => 'company_name'],
            ['value' => $request->company_name]
        );

        return redirect()->back()->with('success', 'Company name updated successfully.');
    }
}
