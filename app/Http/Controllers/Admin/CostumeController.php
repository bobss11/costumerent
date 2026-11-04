<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Costume;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CostumeController extends Controller
{
    /**
     * List all costumes.
     */
    public function index(): View
    {
        $costumes = Costume::with('category')
            ->orderBy('code')
            ->get();

        return view('admin.costumes.index', [
            'costumes' => $costumes,
        ]);
    }

    /**
     * Show the create form.
     */
    public function create(): View
    {
        return view('admin.costumes.create', [
            'costume' => new Costume(['is_active' => true]),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    /**
     * Persist a new costume.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->merge(['is_active' => $request->boolean('is_active')]);
        $data = $this->validateData($request);

        // Default available stock to total unless explicitly provided.
        if (! isset($data['stock_available']) || $data['stock_available'] === null) {
            $data['stock_available'] = $data['stock_total'];
        }

        Costume::create($data);

        return redirect()
            ->route('admin.costumes.index')
            ->with('status', "Kostum {$data['name']} berhasil ditambahkan.");
    }

    /**
     * Show the edit form.
     */
    public function edit(Costume $costume): View
    {
        return view('admin.costumes.edit', [
            'costume' => $costume,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    /**
     * Update an existing costume.
     */
    public function update(Request $request, Costume $costume): RedirectResponse
    {
        $request->merge(['is_active' => $request->boolean('is_active')]);
        $data = $this->validateData($request, $costume);

        if (! isset($data['stock_available']) || $data['stock_available'] === null) {
            $data['stock_available'] = $data['stock_total'];
        }

        $costume->update($data);

        return redirect()
            ->route('admin.costumes.index')
            ->with('status', "Kostum {$costume->name} berhasil diperbarui.");
    }

    /**
     * Delete a costume.
     */
    public function destroy(Costume $costume): RedirectResponse
    {
        $name = $costume->name;
        $costume->delete();

        return redirect()
            ->route('admin.costumes.index')
            ->with('status', "Kostum {$name} berhasil dihapus.");
    }

    /**
     * Shared validation for store/update.
     *
     * @return array<string, mixed>
     */
    private function validateData(Request $request, ?Costume $costume = null): array
    {
        $codeRule = 'unique:costumes,code'.($costume ? ','.$costume->id : '');

        return $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'code' => ['required', 'string', 'max:50', $codeRule],
            'name' => ['required', 'string', 'max:255'],
            'size' => ['required', 'in:S,M,L,XL,All Size'],
            'gender' => ['required', 'in:Pria,Wanita,Unisex'],
            'rental_price_per_day' => ['required', 'numeric', 'min:0'],
            'deposit_amount' => ['required', 'numeric', 'min:0'],
            'late_fee_per_day' => ['required', 'numeric', 'min:0'],
            'stock_total' => ['required', 'integer', 'min:0'],
            'stock_available' => ['nullable', 'integer', 'min:0'],
            'accessories_included' => ['required', 'string', 'max:1000'],
            'image_url' => ['required', 'string', 'max:2048'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}
