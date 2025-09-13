<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServerRequest;
use App\Models\Server;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class ServerController extends Controller
{
    use ApiResponse;

    // List all servers with optional pagination
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $query = Server::query()->select(['id', 'name', 'ip_address', 'provider', 'status', 'cpu_cores', 'ram_mb', 'storage_gb']);

        if ($request->filled('provider')) {
            $query->where('provider', $request->provider);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $servers = $query->orderBy('created_at', 'desc')->paginate($perPage);
        return $this->success($servers);
    }

    // Create server
    public function store(ServerRequest $request)
    {
        $server = Server::create($request->validated());
        return $this->success($server, 'Server created', 201);
    }

    // Show server details
    public function show(Server $server)
    {
        return $this->success($server);
    }

    // Update server
    public function update(ServerRequest $request, Server $server)
    {
        $server->update($request->validated());
        return $this->success($server, 'Server updated');
    }

    // Delete server
    public function destroy(Server $server)
    {
        $server->delete();
        return $this->success([], 'Server deleted');
    }

    // Bulk actions
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,activate,deactivate',
            'ids' => 'required|array',
            'ids.*' => 'exists:servers,id',
        ]);

        $servers = Server::whereIn('id', $request->ids);

        switch ($request->action) {
            case 'delete':
                $servers->delete();
                break;
            case 'activate':
                $servers->update(['status' => 'active']);
                break;
            case 'deactivate':
                $servers->update(['status' => 'inactive']);
                break;
        }

        return $this->success([
            'message' => 'Bulk action performed successfully.',
            'action' => $request->action,
            'count' => count($request->ids)
        ]);
    }

    // Simulate slow query
    public function slowList()
    {
        $servers = Server::all();
        usleep(500000); // simulate delay
        return $this->success($servers);
    }

    // Optimized query
    public function optimizedList(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        $servers = Server::select(['id', 'name', 'ip_address', 'provider', 'status', 'cpu_cores', 'ram_mb', 'storage_gb'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends($request->query());

        return $this->success($servers);
    }
}
