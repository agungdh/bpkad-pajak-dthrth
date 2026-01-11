<?php

namespace App\Http\Controllers;

use App\Http\Requests\KodePajakRequest;
use App\Models\KodePajak;

class KodePajakController extends Controller
{
    public function index()
    {
        $query = KodePajak::query();

        // Add search filter if search parameter exists
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        // Add sorting
        if ($sortBy = request('sort_by')) {
            $direction = request('sort_order', 'asc');
            // Allow sorting by specific columns only for safety
            if (in_array($sortBy, ['kode', 'nama'])) {
                $query->orderBy($sortBy, $direction);
            }
        }

        return $query->uuidCursorPaginate();
    }

    public function store(KodePajakRequest $request)
    {
        $form = $request->validated();

        $kodePajak = new KodePajak;
        $this->saveData($kodePajak, $form);

        return $kodePajak;
    }

    public function show(KodePajak $kodePajak)
    {
        return $kodePajak;
    }

    public function update(KodePajakRequest $request, KodePajak $kodePajak)
    {
        $form = $request->validated();

        $this->saveData($kodePajak, $form);

        return $kodePajak;
    }

    public function destroy(KodePajak $kodePajak)
    {
        $kodePajak->delete();
    }

    private function saveData(KodePajak $kodePajak, mixed $form): void
    {
        $kodePajak->kode = $form['kode'];
        $kodePajak->nama = $form['nama'];
        $kodePajak->save();
    }
}
