<?php

namespace App\Http\Controllers;

use App\Http\Requests\SkpdCreateUpdateRequest;
use App\Models\Skpd;

class SkpdController extends Controller
{
    public function index()
    {
        $query = Skpd::query();

        // Add search filter if search parameter exists
        if ($search = request('search')) {
            $query->where('nama', 'like', "%{$search}%");
        }

        // Add sorting
        if ($sortBy = request('sort_by')) {
            $direction = request('sort_order', 'asc');
            // Allow sorting by specific columns only for safety
            if (in_array($sortBy, ['nama'])) {
                $query->orderBy($sortBy, $direction);
            }
        }

        return $query->uuidCursorPaginate();
    }

    public function store(SkpdCreateUpdateRequest $request)
    {
        $form = $request->validated();

        $skpd = new Skpd;
        $this->saveData($skpd, $form);

        return $skpd;
    }

    public function show(Skpd $skpd)
    {
        return $skpd;
    }

    public function update(SkpdCreateUpdateRequest $request, Skpd $skpd)
    {
        $form = $request->validated();

        $this->saveData($skpd, $form);

        return $skpd;
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
