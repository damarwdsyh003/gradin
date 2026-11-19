<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourierRequest;
use App\Http\Requests\UpdateCourierRequest;
use App\Models\Courier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class CourierController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Courier::query();

        if ($request->filled('search')) {
            foreach (preg_split('/\s+/', trim((string) $request->query('search')), -1, PREG_SPLIT_NO_EMPTY) as $term) {
                $query->where('name', 'like', '%'.$term.'%');
            }
        }

        if ($request->filled('level')) {
            $levels = array_values(array_filter(array_map('trim', explode(',', (string) $request->query('level'))), 'strlen'));
            Validator::make(['level' => $levels], ['level' => ['array'], 'level.*' => ['integer', 'between:1,5']])->validate();
            $query->whereIn('level', $levels);
        }

        if ($request->query('sort') === 'registered_at') {
            $query->orderByDesc('created_at');
        } else {
            $query->orderBy('name')->orderBy('id');
        }

        return response()->json($query->paginate((int) $request->query('per_page', 15)));
    }

    public function store(StoreCourierRequest $request): JsonResponse
    {
        return response()->json(Courier::create($request->validated()), 201);
    }

    public function show(Courier $courier): JsonResponse
    {
        return response()->json($courier);
    }

    public function update(UpdateCourierRequest $request, Courier $courier): JsonResponse
    {
        $courier->update($request->validated());

        return response()->json($courier->refresh());
    }

    public function destroy(Courier $courier): Response
    {
        $courier->delete();

        return response()->noContent();
    }
}
