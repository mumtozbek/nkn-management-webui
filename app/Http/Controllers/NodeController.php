<?php

namespace App\Http\Controllers;

use App\DataTables\NodesDataTable;
use App\Models\Account;
use App\Models\Node;
use App\Models\Proposal;
use App\Models\Uptime;
use Illuminate\Http\Request;

class NodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param NodesDataTable $dataTable
     * @return \Illuminate\Http\Response
     */
    public function index(NodesDataTable $dataTable)
    {
        return $dataTable->render('nodes.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $accounts = Account::select(['providers.*', 'accounts.*'])->leftJoin('providers', 'provider_id', '=', 'providers.id')->orderBy('providers.name', 'ASC')->orderBy('accounts.name', 'ASC')->get();

        return view('nodes.create', compact('accounts'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Node $node)
    {
        $data = $this->validate($request, $node->rules());

        $node->fill($data)->save();

        return redirect(route('nodes.index'))
            ->with('flash', 'Node has been created!');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Node $node
     * @return \Illuminate\Http\Response
     */
    public function show(Node $node)
    {
        $speedChartData = Uptime::getChartData($node->id);
        $proposalChartData = Proposal::getChartData($node->id);

        return view('nodes.show', compact('node', 'speedChartData', 'proposalChartData'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Node $node
     * @return \Illuminate\Http\Response
     */
    public function edit(Node $node)
    {
        $accounts = Account::select(['providers.*', 'accounts.*'])->leftJoin('providers', 'provider_id', '=', 'providers.id')->orderBy('providers.name', 'ASC')->orderBy('accounts.name', 'ASC')->get();

        return view('nodes.edit', compact('node', 'accounts'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Node $node
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Node $node)
    {
        $data = $this->validate($request, $node->rules());

        $node->update($data);

        return redirect(route('nodes.index'))
            ->with('flash', 'Node has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Node $node
     * @return \Illuminate\Http\Response
     */
    public function destroy(Node $node)
    {
        $node->delete();

        return redirect(route('nodes.index'))
            ->with('flash', 'Node has been deleted!');
    }
}
