<?php

namespace App\Http\Controllers;

use App\Models\OvertimeLog;
use Illuminate\Http\Request;
use App\Traits\AuditLogger;


class OvertimeLogController extends Controller
{

    use AuditLogger;
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $overtimes = auth()->user()->overtimes()
        ->orderBy('date', 'desc')
        ->get();

    return view('time.overtime', compact('overtimes'));
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(OvertimeLog $overtimeLog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OvertimeLog $overtimeLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OvertimeLog $overtimeLog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OvertimeLog $overtimeLog)
    {
        //
    }
}
