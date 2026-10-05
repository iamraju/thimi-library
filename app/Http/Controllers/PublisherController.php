<?php

namespace App\Http\Controllers;

use App\Models\Publisher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublisherController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('catalog/publishers/Index', [
            'publishers' => Publisher::query()
                ->when($request->string('search')->isNotEmpty(), fn ($query) => $query->where('title', 'like', '%'.$request->string('search').'%'))
                ->withCount('books')->orderBy('title')->paginate(15)->withQueryString(),
            'filters' => $request->only('search'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('catalog/publishers/Form');
    }

    public function store(Request $request): RedirectResponse
    {
        Publisher::create($this->validated($request));

        return to_route('publishers.index')->with('success', __('Publisher created.'));
    }

    public function edit(Publisher $publisher): Response
    {
        return Inertia::render('catalog/publishers/Form', ['publisher' => $publisher]);
    }

    public function update(Request $request, Publisher $publisher): RedirectResponse
    {
        $publisher->update($this->validated($request, $publisher));

        return to_route('publishers.index')->with('success', __('Publisher updated.'));
    }

    public function destroy(Publisher $publisher): RedirectResponse
    {
        if ($publisher->books()->exists()) {
            return back()->withErrors(['publisher' => __('This publisher still has books assigned to it.')]);
        }

        $publisher->delete();

        return to_route('publishers.index')->with('success', __('Publisher deleted.'));
    }

    /** @return array{title: string, address: ?string, telephone: ?string, mobile: ?string, contact_person: ?string, status: int} */
    private function validated(Request $request, ?Publisher $publisher = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255', 'unique:publishers,title,'.$publisher?->id],
            'address' => ['nullable', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:40'],
            'mobile' => ['nullable', 'string', 'max:40'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'integer', 'in:0,1'],
        ]);
    }
}
