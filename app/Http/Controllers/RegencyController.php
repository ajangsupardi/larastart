<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegencyRequest;
use App\Http\Requests\UpdateRegencyRequest;
use App\Http\Resources\RegencyResource;
use App\Models\Regency;
use App\Services\GeoCacheService;
use Inertia\Inertia;
use Inertia\Response;

class RegencyController extends Controller
{
    public function index(): Response
    {
        $search = request('search');
        $provinceId = request('province_id');

        $regencies = RegencyResource::collection(
            Regency::query()
                ->with('province')
                ->with('creator')
                ->when($search, fn ($query, $search) => $query
                    ->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($search).'%']))
                ->when($provinceId, fn ($query, $provinceId) => $query
                    ->where('province_id', $provinceId))
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->withQueryString()
        );

        return Inertia::render('Regencies/Index', [
            'regencies' => $regencies,
            'filters' => request()->only('search', 'province_id'),
            'provinces' => GeoCacheService::getProvinces(),
            'stats' => [
                'total' => ['label' => 'Total Regencies', 'value' => Regency::count(), 'trend' => 0],
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Regencies/Create', [
            'provinces' => GeoCacheService::getProvinces(),
        ]);
    }

    public function store(StoreRegencyRequest $request)
    {
        Regency::create([...$request->validated(), 'created_by' => auth()->id()]);

        return to_route('regencies.index')
            ->with('success', 'Regency created successfully.');
    }

    public function edit(Regency $regency): Response
    {
        $regency->load('province');

        return Inertia::render('Regencies/Edit', [
            'regency' => [
                'id' => $regency->id,
                'name' => $regency->name,
                'province_id' => $regency->province_id,
                'province' => $regency->province,
            ],
            'provinces' => GeoCacheService::getProvinces(),
        ]);
    }

    public function update(UpdateRegencyRequest $request, Regency $regency)
    {
        $regency->update($request->validated());

        return to_route('regencies.index')
            ->with('success', 'Regency updated successfully.');
    }

    public function destroy(Regency $regency)
    {
        $regency->delete();

        return to_route('regencies.index')
            ->with('success', 'Regency deleted successfully.');
    }
}
