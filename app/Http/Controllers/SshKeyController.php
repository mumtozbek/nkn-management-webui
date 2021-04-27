<?php

namespace App\Http\Controllers;

use App\DataTables\SshKeysDataTable;
use App\Models\SshKey;
use App\Models\Provider;
use Illuminate\Http\Request;

class SshKeyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param SshKeysDataTable $dataTable
     * @return \Illuminate\Http\Response
     */
    public function index(SshKeysDataTable $dataTable)
    {
        return $dataTable->render('ssh_keys.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('ssh_keys.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, SshKey $ssh_key)
    {
        $data = $this->validate($request, $ssh_key->rules());

        $ssh_key->fill($data)->save();

        return redirect(route('ssh-keys.index'))
            ->with('flash', 'SshKey has been created!');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\SshKey $ssh_key
     * @return \Illuminate\Http\Response
     */
    public function show(SshKey $ssh_key)
    {
        return view('ssh_keys.show', compact('ssh_key'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\SshKey $ssh_key
     * @return \Illuminate\Http\Response
     */
    public function edit(SshKey $ssh_key)
    {
        return view('ssh_keys.edit', compact('ssh_key'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\SshKey $ssh_key
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SshKey $ssh_key)
    {
        $data = $this->validate($request, $ssh_key->rules());

        $ssh_key->update($data);

        return redirect(route('ssh-keys.index'))
            ->with('flash', 'SshKey has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\SshKey $ssh_key
     * @return \Illuminate\Http\Response
     */
    public function destroy(SshKey $ssh_key)
    {
        $ssh_key->delete();

        return redirect(route('ssh-keys.index'))
            ->with('flash', 'SshKey has been deleted!');
    }
}
