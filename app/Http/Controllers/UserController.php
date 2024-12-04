<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\CreateRequest;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:create-employee')->only('store');
        $this->middleware('permission:update-employee')->only('update');
        $this->middleware('permission:delete-employee')->only('destroy');
        $this->middleware('permission:restore-employee')->only('restore');

    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $limit = $request->input('limit', 10);

        $data = User::when(auth()->user()->hasRole('employee'), function ($q) {
            $q->whereHas('roles', function ($subQuery) {
                $subQuery->where('name', 'employee'); // Menambahkan kondisi untuk hanya mengambil user dengan role 'manager'
            });
        })->when($search, function ($q) use ($search) {
            $q->where('name', 'ILIKE', '%'.$search.'%');
        })->orderBy('updated_at', 'desc')->simplePaginate($limit);

        return $this->handleResponse(true, 'get data project', $data, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            $validated['email_verified_at'] = now();
            $createUser = User::create($validated);
            $createUser->syncRoles('employee');
        });

        return $this->handleResponse(true, 'created new user ', null, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {

        $user->load('roles.permissions');

        return $this->handleResponse(true, 'retrieved user', $user, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateRequest $request, User $user)
    {

        $validated = $request->validated();

        $user->update($validated);

        return $this->handleResponse(true, 'User updated successfully', null, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {

        $user->delete(); // Then delete the user

        return $this->handleResponse(true, 'User deleted successfully', null, 200);
    }

    public function restore(User $user)
    {
        $user->restore();

        return $this->handleResponse(true, 'User restored successfully', null, 200);
    }

    public function listRole(Request $request)
    {
        $search = $request->input('search', '');
        $limit = $request->input('limit', 10);

        $data = Role::when($search, function ($q) use ($search) {
            $q->where('name', 'ILIKE', '%'.$search.'%');
        })->orderBy('updated_at', 'desc')->paginate($limit);

        return $this->handleResponse(true, 'get data roles', $data, 200);
    }
}
