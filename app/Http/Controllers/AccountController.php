<?php

namespace App\Http\Controllers;

use App\DataTables\AccountsDataTable;
use App\Models\Account;
use App\Models\Provider;
use App\Models\SshKey;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param AccountsDataTable $dataTable
     * @return \Illuminate\Http\Response
     */
    public function index(AccountsDataTable $dataTable)
    {
        return $dataTable->render('accounts.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $providers = Provider::orderBy('name', 'ASC')->get();
        $ssh_keys = SshKey::all();

        return view('accounts.create', compact('providers', 'ssh_keys'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Account $account)
    {
        $data = $this->validate(request(), $account->rules());

        $account->fill($data)->save();

        return redirect(route('accounts.index'))
            ->with('flash', 'Account has been created!');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Account $account
     * @return \Illuminate\Http\Response
     */
    public function show(Account $account)
    {
        return view('accounts.show', compact('account'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Account $account
     * @return \Illuminate\Http\Response
     */
    public function edit(Account $account)
    {
        $providers = Provider::orderBy('name', 'ASC')->get();
        $ssh_keys = SshKey::all();

        return view('accounts.edit', compact('account', 'providers', 'ssh_keys'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Account $account
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Account $account)
    {
        $data = $this->validate($request, $account->rules());

        $account->update($data);

        return redirect(route('accounts.index'))
            ->with('flash', 'Account has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Account $account
     * @return \Illuminate\Http\Response
     */
    public function destroy(Account $account)
    {
        $account->delete();

        return redirect(route('accounts.index'))
            ->with('flash', 'Account has been deleted!');
    }
}
