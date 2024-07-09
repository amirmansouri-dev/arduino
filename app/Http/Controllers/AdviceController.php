<?php

namespace App\Http\Controllers;
use App\Models\Advice;

use Illuminate\Http\Request;

class AdviceController extends Controller
{
    public function index()
    {
        return Advice::all();
    }

    public function show($id)
    {
        return Advice::find($id);
    }

    public function store(Request $request)
    {
        $advice = Advice::create($request->all());
        return response()->json($advice, 201);
    }

    public function update(Request $request, $id)
    {
        $advice = Advice::findOrFail($id);
        $advice->update($request->all());
        return response()->json($advice, 200);
    }

    public function delete($id)
    {
        Advice::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
