<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $query = User::with(['skpd', 'roles']);

        // Add search filter if search parameter exists
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Add sorting
        if ($sortBy = request('sort_by')) {
            $direction = request('sort_order', 'asc');
            // Allow sorting by specific columns only for safety
            if (in_array($sortBy, ['name', 'username'])) {
                $query->orderBy($sortBy, $direction);
            }
        }

        return $query->uuidCursorPaginate();
    }

    public function store(UserRequest $request)
    {
        $form = $request->validated();

        $user = new User;
        $this->saveData($user, $form);

        return $user->load(['skpd', 'roles']);
    }

    public function show(User $user)
    {
        return $user->load(['skpd', 'roles']);
    }

    public function update(UserRequest $request, User $user)
    {
        $form = $request->validated();

        $this->saveData($user, $form);

        return $user->load(['skpd', 'roles']);
    }

    public function destroy(User $user)
    {
        $user->delete();
    }

    /**
     * Get list of available roles.
     */
    public function roles(): JsonResponse
    {
        $roles = Role::select('id', 'name')->orderBy('name')->get();
        return response()->json($roles);
    }

    private function saveData(User $user, mixed $form): void
    {
        $user->name = $form['name'];
        $user->username = $form['username'];

        // Convert SKPD uuid to id
        if (!empty($form['skpd_id'])) {
            $skpd = \App\Models\Skpd::where('uuid', $form['skpd_id'])->first();
            $user->skpd_id = $skpd?->id;
        } else {
            $user->skpd_id = null;
        }

        if (!empty($form['password'])) {
            $user->password = $form['password'];
        }

        $user->save();

        // Sync role
        if (isset($form['role'])) {
            $user->syncRoles([$form['role']]);
        }
    }
}
