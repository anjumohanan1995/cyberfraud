<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use MongoDB\Client;

class BankReportController extends Controller
{
    /**
     * Display a listing of the transactions.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return view('dashboard.bank-reports.index');
    }

    public function getBankDetailsByDate(Request $request)
    {
        $date = $request->input('date');

        $client = new Client(env('MONGODB_CONNECTION_STRING'));
        $db = $client->selectDatabase('cyber');
        $transactionsCollection = $db->transactions;

        $pipeline = [
            [
                '$match' => [
                    'Layer' => 1,
                    'transaction_date' => $date
                ]
            ],
            [
                '$lookup' => [
                    'from' => 'complaints',
                    'localField' => 'acknowledgement_no',
                    'foreignField' => 'acknowledgement_no',
                    'as' => 'complaints_data'
                ]
            ],
            [
                '$unwind' => '$complaints_data'
            ],
            [
                '$group' => [
                    '_id' => '$complaints_data.district',
                    'transactions' => [
                        '$push' => [
                            'transaction_id_or_utr_no' => '$transaction_id_or_utr_no',
                            'transaction_amount' => '$transaction_amount',
                            'bank' => '$bank',
                            'action_taken_by_bank' => '$action_taken_by_bank'
                        ]
                    ]
                ]
            ]
        ];

        $results = $transactionsCollection->aggregate($pipeline)->toArray();

        return view('bank_reports.date', compact('results'));
    }
    /**
     * Show the form for creating a new transaction.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('bank_reports.create');
    }

    /**
     * Store a newly created transaction in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaction_id' => 'required|unique:transactions',
            'amount' => 'required|numeric',
            'transaction_date' => 'required|date',
            'status' => 'required|string',
        ]);

        Transaction::create($validated);

        return redirect()->route('bank_reports.index')
                         ->with('success', 'Transaction created successfully.');
    }

    /**
     * Display the specified transaction.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        return view('bank_reports.show', compact('transaction'));
    }

    /**
     * Show the form for editing the specified transaction.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction)
    {
        return view('bank_reports.edit', compact('transaction'));
    }

    /**
     * Update the specified transaction in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'transaction_id' => 'required|unique:transactions,transaction_id,' . $transaction->id,
            'amount' => 'required|numeric',
            'transaction_date' => 'required|date',
            'status' => 'required|string',
        ]);

        $transaction->update($validated);

        return redirect()->route('bank_reports.index')
                         ->with('success', 'Transaction updated successfully.');
    }

    /**
     * Remove the specified transaction from storage.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        return redirect()->route('bank_reports.index')
                         ->with('success', 'Transaction deleted successfully.');
    }
}
