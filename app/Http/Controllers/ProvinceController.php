<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProvinceRequest;
use App\Http\Requests\UpdateProvinceRequest;
use App\Http\Resources\ProvinceResource;
use App\Models\Province;
use Inertia\Inertia;
use Inertia\Response;

class ProvinceController extends Controller
{
    public function index(): Response
    {
        $search = request('search');

        $provinces = ProvinceResource::collection(
            Province::query()
                ->when($search, fn ($query, $search) => $query
                    ->where(function ($q) use ($search) {
                        $q->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($search).'%'])
                            ->orWhereRaw('LOWER(code) LIKE ?', ['%'.strtolower($search).'%']);
                    }))
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->withQueryString()
        );

        return Inertia::render('Provinces/Index', [
            'provinces' => $provinces,
            'filters' => request()->only('search'),
            'stats' => [
                'total' => ['label' => 'Total Provinces', 'value' => Province::count(), 'trend' => 0],
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Provinces/Create');
    }

    public function store(StoreProvinceRequest $request)
    {
        Province::create([...$request->validated(), 'created_by' => auth()->id()]);

        return to_route('provinces.index')
            ->with('success', 'Province created successfully.');
    }

    public function edit(Province $province): Response
    {
        return Inertia::render('Provinces/Edit', [
            'province' => [
                'id' => $province->id,
                'name' => $province->name,
                'code' => $province->code,
            ],
        ]);
    }

    public function update(UpdateProvinceRequest $request, Province $province)
    {
        $province->update($request->validated());

        return to_route('provinces.index')
            ->with('success', 'Province updated successfully.');
    }

    public function destroy(Province $province)
    {
        $province->delete();

        return to_route('provinces.index')
            ->with('success', 'Province deleted successfully.');
    }
}
