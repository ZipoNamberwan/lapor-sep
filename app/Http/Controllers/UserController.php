<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('user/index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'pml' => 'required',
            'password' => 'required',
        ]);

        if (!Auth::check()) {
            return abort(403);
        }
        $admin = User::find(Auth::user()->id);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'pml' => $request->pml,
            'password' => bcrypt($request->password),
            'regency_id' => $admin->regency->id
        ]);
        $user->assignRole('pcl');

        return redirect('/users')->with('success-create', 'Petugas telah ditambah!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        return view('user/edit', ['user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'pml' => 'required',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'pml' => $request->pml,
            'password' => $request->password != $user->password ?  bcrypt($request->password) : $user->password,
        ]);

        return redirect('/users')->with('success-create', 'Petugas telah diubah!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Auth::check()) {
            return abort(403);
        }

        try {
            User::destroy($id);
        } catch (Exception $e) {
            User::find($id)->assignRole('pcl');

            return redirect('/users')->with('error-delete', 'Petugas gagal dihapus, karena ada sampel yang dientri oleh petugas tsb.');
        }
        return redirect('/users')->with('success-edit', 'Petugas telah dihapus!');
    }

    public function getData(Request $request)
    {
        if (!Auth::check()) {
            return abort(403);
        }
        $admin = User::find(Auth::user()->id);

        $recordsTotal = User::role('pcl')->where(['regency_id' => $admin->regency->id])->get()->count();
        $orderColumn = 'name';
        $orderDir = 'desc';
        if ($request->order != null) {
            if ($request->order[0]['dir'] == 'asc') {
                $orderDir = 'asc';
            } else {
                $orderDir = 'desc';
            }
            if ($request->order[0]['column'] == '1') {
                $orderColumn = 'name';
            } else if ($request->order[0]['column'] == '2') {
                $orderColumn = 'email';
            }
        }

        $searchkeyword = $request->search['value'];
        $users = User::role('pcl')->where(['regency_id' => $admin->regency->id])->get();
        if ($searchkeyword != null) {
            $users = $users->filter(function ($q) use (
                $searchkeyword
            ) {
                return Str::contains(strtolower($q->name), strtolower($searchkeyword)) ||
                    Str::contains(strtolower($q->email), strtolower($searchkeyword));
            });
        }
        $recordsFiltered = $users->count();

        if ($orderDir == 'asc') {
            $users = $users->sortBy($orderColumn);
        } else {
            $users = $users->sortByDesc($orderColumn);
        }

        if ($request->length != -1) {
            $users = $users->skip($request->start)
                ->take($request->length);
        }

        $usersArray = array();
        $i = $request->start + 1;
        foreach ($users as $user) {
            $userData = array();
            $userData["index"] = $i;
            $userData["id"] = $user->id;
            $userData["name"] = $user->name;
            $userData["email"] = $user->email;
            $userData["pml"] = $user->pml;
            $usersArray[] = $userData;
            $i++;
        }
        return json_encode([
            "draw" => $request->draw,
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $usersArray
        ]);
    }
}
