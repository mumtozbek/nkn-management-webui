<?php

namespace App\Http\Controllers;

use App\DataTables\ProvidersDataTable;
use App\Models\Provider;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ProvidersDataTable $dataTable
     * @return \Illuminate\Http\Response
     */
    public function index(ProvidersDataTable $dataTable)
    {
        return $dataTable->render('providers.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('providers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Provider $provider)
    {
        $data = $this->validate($request, $provider->rules());

        $provider->fill($data)->save();

        return redirect(route('providers.index'))
            ->with('flash', 'Provider has been created!');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Provider $provider
     * @return \Illuminate\Http\Response
     */
    public function show(Provider $provider)
    {
        return view('providers.show', compact('provider'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Provider $provider
     * @return \Illuminate\Http\Response
     */
    public function edit(Provider $provider)
    {
        return view('providers.edit', compact('provider'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Provider $provider
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Provider $provider)
    {
        $data = $this->validate($request, $provider->rules());

        $provider->update($data);

        return redirect(route('providers.index'))
            ->with('flash', 'Provider has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Provider $provider
     * @return \Illuminate\Http\Response
     */
    public function destroy(Provider $provider)
    {
        $provider->delete();

        return redirect(route('providers.index'))
            ->with('flash', 'Provider has been deleted!');
    }
}
