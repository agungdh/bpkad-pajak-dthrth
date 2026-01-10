<?php

namespace App\Http\Controllers;

use App\Http\Requests\SkpdCreateUpdateRequest;
use App\Models\Skpd;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SkpdController extends Controller
{
    public function datatable(Request $request)
    {
        $datas = Skpd::query();

        return DataTables::of($datas)
            ->addColumn('action', function ($row) {
                return view('pages.skpd.action', ['row' => $row])->render();
            })
            ->make();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.skpd.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.skpd.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SkpdCreateUpdateRequest $request)
    {
        $form = $request->validated();

        $skpd = new Skpd;
        $this->saveData($skpd, $form);

        request()->session()->flash('success', 'Skpd berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Skpd $skpd)
    {
        return $skpd;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Skpd $skpd)
    {
        return view('pages.skpd.form', compact([
            'skpd',
        ]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SkpdCreateUpdateRequest $request, Skpd $skpd)
    {
        $form = $request->validated();

        $this->saveData($skpd, $form);

        request()->session()->flash('success', 'Skpd berhasil disimpan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Skpd $skpd)
    {
        $skpd->delete();
    }

    private function saveData(Skpd $skpd, mixed $form): void
    {
        $skpd->nama = $form['nama'];
        $skpd->save();
    }
}
