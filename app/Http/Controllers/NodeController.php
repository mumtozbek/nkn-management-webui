<?php

namespace App\Http\Controllers;

use App\DataTables\NodesDataTable;
use App\Models\Node;
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
        return view('nodes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Node $node)
    {
        $data = $this->validate(request(), $node->rules());

        $node->fill($data)->save();

        return redirect($node->path())
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
        return view('nodes.show', compact('node'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Node $node
     * @return \Illuminate\Http\Response
     */
    public function edit(Node $node)
    {
        return view('nodes.edit', compact('node'));
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

        return redirect($node->path())
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
        //
    }
}
