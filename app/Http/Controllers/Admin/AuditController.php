<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog as AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditController extends Controller
{
    public function index(Request $request): View
    {
        $category = $request->input('category');
        $search = $request->input('q');

        $auditLogs = AuditLog::with('admin')
            ->when($category, fn($q) => $q->where('category', $category))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('action', 'like', "%$search%")
                      ->orWhereHas('admin', fn($q) => $q->where('name', 'like', "%$search%"));
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.audit.index', compact('auditLogs', 'category'));
    }
}
