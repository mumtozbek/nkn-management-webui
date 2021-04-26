<?php

namespace App\Http\Controllers;

use App\DataTables\WalletsDataTable;
use App\Models\Wallet;
use App\Models\Node;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param WalletsDataTable $dataTable
     * @return \Illuminate\Http\Response
     */
    public function index(WalletsDataTable $dataTable)
    {
        return $dataTable->render('wallets.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $nodes = Node::orderBy('host', 'ASC')->get();

        return view('wallets.create', compact('nodes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Wallet $wallet)
    {
        $data = $this->validate($request, $wallet->rules());

        $wallet->fill($data)->save();

        return redirect(route('wallets.index'))
            ->with('flash', 'Wallet has been created!');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Wallet $wallet
     * @return \Illuminate\Http\Response
     */
    public function show(Wallet $wallet)
    {
        return view('wallets.show', compact('wallet'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Wallet $wallet
     * @return \Illuminate\Http\Response
     */
    public function edit(Wallet $wallet)
    {
        $nodes = Node::orderBy('host', 'ASC')->get();

        return view('wallets.edit', compact('wallet', 'nodes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Wallet $wallet
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Wallet $wallet)
    {
        $data = $this->validate($request, $wallet->rules());

        $wallet->update($data);

        return redirect(route('wallets.index'))
            ->with('flash', 'Wallet has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Wallet $wallet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Wallet $wallet)
    {
        $wallet->delete();

        return redirect(route('wallets.index'))
            ->with('flash', 'Wallet has been deleted!');
    }
}
