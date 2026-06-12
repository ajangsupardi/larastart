<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOccupationRequest;
use App\Http\Requests\UpdateOccupationRequest;
use App\Http\Resources\OccupationResource;
use App\Models\Occupation;
use Inertia\Inertia;
use Inertia\Response;

class OccupationController extends Controller
{
    public function index(): Response
    {
        $search = request('search');

        $occupations = OccupationResource::collection(
            Occupation::query()->with('creator')
                ->when($search, fn ($query, $search) => $query
                    ->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($search).'%']))
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->withQueryString()
        );

        return Inertia::render('Occupations/Index', [
            'occupations' => $occupations,
            'filters' => request()->only('search'),
            'stats' => [
                'total' => ['label' => 'Total Occupations', 'value' => Occupation::count(), 'trend' => 0],
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Occupations/Create');
    }

    public function store(StoreOccupationRequest $request)
    {
        Occupation::create([...$request->validated(), 'created_by' => auth()->id()]);

        return to_route('occupations.index')
            ->with('success', 'Occupation created successfully.');
    }

    public function edit(Occupation $occupation): Response
    {
        return Inertia::render('Occupations/Edit', [
            'occupation' => [
                'id' => $occupation->id,
                'name' => $occupation->name,
            ],
        ]);
    }

    public function update(UpdateOccupationRequest $request, Occupation $occupation)
    {
        $occupation->update($request->validated());

        return to_route('occupations.index')
            ->with('success', 'Occupation updated successfully.');
    }

    public function destroy(Occupation $occupation)
    {
        $occupation->delete();

        return to_route('occupations.index')
            ->with('success', 'Occupation deleted successfully.');
    }
}
