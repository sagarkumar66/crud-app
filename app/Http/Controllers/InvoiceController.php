<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceStoreRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use App\DataTables\UsersDataTable;
use Illuminate\Support\Facades\DB;
// use Datatables;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('invoice.index');
    }

    public function invoicesData()
    {
        $invoices = Invoice::join('users', 'invoices.user_id', '=', 'users.id')
        ->select('invoices.*', 'users.first_name', 'users.last_name');

        return DataTables::of($invoices)
        ->addColumn('action', function ($invoice) {
            $editUrl = route('invoices.edit', $invoice->id);
            return '<a href="' . $editUrl . '" class="btn btn-primary">Edit</a>';
        })
        ->rawColumns(['action'])
        ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        return view('invoice.create_edit', ['users' => $users]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InvoiceStoreRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();
    
        $invoice = new Invoice();
        $invoice->fill($validatedData);
        $invoice->save();

        // Save invoice items
        foreach ($validatedData['item_name'] as $index => $itemName) {
            $invoice->items()->create([
                'name' => $itemName,
                'quantity' => $validatedData['item_quantity'][$index],
                'amount' => $validatedData['item_amount'][$index],
            ]);
        }

        return Redirect::route('invoices.index')->with('status', 'invoice-created');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoiceItems = InvoiceItem::where('invoice_id', $id)->get();
        $users = User::all();

        return view('invoice.create_edit', compact('invoice', 'invoiceItems', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(InvoiceStoreRequest $request, Invoice $invoice): RedirectResponse
    {
        $validatedData = $request->validated();

        // Update the invoice
        $invoice->update($validatedData);

        // Update invoice items
        $invoice->items()->delete(); // Delete existing items before adding new ones
        foreach ($validatedData['item_name'] as $index => $itemName) {
            $invoice->items()->create([
                'name' => $itemName,
                'quantity' => $validatedData['item_quantity'][$index],
                'amount' => $validatedData['item_amount'][$index],
            ]);
        }

        return redirect()->route('invoices.index', $invoice->id)->with('status', 'invoice-updated');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
