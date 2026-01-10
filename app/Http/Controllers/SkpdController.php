<?php

namespace App\Http\Controllers;

use App\Http\Requests\SkpdCreateUpdateRequest;
use App\Models\Skpd;

class SkpdController extends Controller
{
    public function index()
    {
        return Skpd::uuidCursorPaginate();
    }

    public function store(SkpdCreateUpdateRequest $request)
    {
        $form = $request->validated();

        $skpd = new Skpd;
        $this->saveData($skpd, $form);
    }

    public function show(Skpd $skpd)
    {
        return $skpd;
    }

    public function update(SkpdCreateUpdateRequest $request, Skpd $skpd)
    {
        $form = $request->validated();

        $this->saveData($skpd, $form);
    }

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
