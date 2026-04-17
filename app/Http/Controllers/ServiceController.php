<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;


class ServiceController extends Controller
{
    private const CORE_TITLES = [
        'Full Service',
        'Drop-Off Service',
        'Self-Service',
    ];
    /**
     * List services (admin table with search + pagination).
     */
    public function list(Request $request)
    {
        $search = trim((string) $request->input('search', ''));

        $services = Service::query()
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($w) use ($search) {
                    $w->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy('sort_order')
            ->orderBy('title')
            ->paginate(10)
            ->withQueryString();

        return view('admin.services.services', compact('services'));
    }

    /**
     * AJAX search rows (returns a rows partial).
     */
    public function ajaxSearch(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $services = Service::query()
            ->when($q !== '', function ($w) use ($q) {
                $w->where(function ($z) use ($q) {
                    $z->where('title', 'like', "%{$q}%")
                      ->orWhere('description', 'like', "%{$q}%");
                });
            })
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        return view('admin.services.partials.rows', compact('services'));
    }

    /**
     * SHOW: Add Service form.
     * View: resources/views/admin/services/add_service.blade.php
     */
    public function addForm()
    {
        return view('admin.services.add_service');
    }

    /**
     * POST: Create Service.
     */
    public function add(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'nullable|boolean',
        ]);

        Service::create([
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'price'       => $validated['price'],
            'sort_order'  => $validated['sort_order'] ?? 0,
            'is_active'   => $request->boolean('is_active'),
        ]);

        return redirect()->route('services')->with('success', 'Service Added Successfully');
    }

    /**
     * SHOW: Edit Service form.
     * View: resources/views/admin/services/edit_service.blade.php
     */
    public function editForm($id)
    {
        $service = Service::findOrFail($id);
        return view('admin.services.edit_service', compact('service'));
    }

    /**
     * POST: Update Service.
     */
    public function edit(Request $request)
    {
        $validated = $request->validate([
            'service_id'  => 'required|exists:services,id',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'nullable|boolean',
        ]);

        Service::where('id', $validated['service_id'])->update([
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'price'       => $validated['price'],
            'sort_order'  => $validated['sort_order'] ?? 0,
            'is_active'   => $request->boolean('is_active'),
        ]);

        return redirect()->route('services')->with('success', 'Service Updated Successfully');
    }

    /**
     * GET: Delete Service.
     */
        /**
     * GET: Delete Service.
     */
    public function delete($id)
    {
        $service = Service::findOrFail($id);

        // 🔒 Block delete for the 3 core services
        if (in_array($service->title, self::CORE_TITLES, true)) {
            return redirect()
                ->route('services')
                ->with('fail', 'This default service cannot be deleted. You can still edit its price/details.');
        }

        $service->delete();

        return redirect()
            ->route('services')
            ->with('delete', 'Service Deleted Successfully');
    }


    
}
