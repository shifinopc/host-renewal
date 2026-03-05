<?php

namespace App\Http\Controllers;

use App\Models\Server;
use Illuminate\Http\Request;

class ServerController extends Controller
{
    public function index()
    {
        $servers = Server::latest()->paginate(10);

        return view('servers.index', compact('servers'));
    }

    public function create()
    {
        return view('servers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'provider' => 'nullable|string|max:255',
            'ip_address' => 'nullable|string|max:255',
            'type' => 'required|in:Shared,VPS,Cloud',
            'notes' => 'nullable|string',
        ]);

        Server::create($data);

        return redirect()->route('servers.index')->with('status', 'Server created successfully.');
    }

    public function edit(Server $server)
    {
        return view('servers.edit', compact('server'));
    }

    public function update(Request $request, Server $server)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'provider' => 'nullable|string|max:255',
            'ip_address' => 'nullable|string|max:255',
            'type' => 'required|in:Shared,VPS,Cloud',
            'notes' => 'nullable|string',
        ]);

        $server->update($data);

        return redirect()->route('servers.index')->with('status', 'Server updated successfully.');
    }

    public function destroy(Server $server)
    {
        $server->delete();

        return redirect()->route('servers.index')->with('status', 'Server deleted.');
    }
}

