<?php

namespace App\Http\Controllers;

use App\Models\Query;
use Illuminate\Http\Request;

class QueryController extends Controller
{
    public function index()
    {
        return Query::all();
    }

    public function show($id)
    {
        return Query::find($id);
    }

    public function store(Request $request)
    {
        $query = Query::create($request->all());
        return response()->json($query, 201);
    }

    public function update(Request $request, $id)
    {
        $query = Query::findOrFail($id);
        $query->update($request->all());
        return response()->json($query, 200);
    }

    public function delete($id)
    {
        Query::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
