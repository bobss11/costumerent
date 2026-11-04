<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Costume;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    /**
     * The costume sizes available for filtering.
     *
     * @var list<string>
     */
    protected array $sizes = ['S', 'M', 'L', 'XL', 'All Size'];

    /**
     * Display the public catalog with interactive filters.
     */
    public function index(Request $request): View
    {
        $query = Costume::query()
            ->with('category')
            ->where('is_active', true);

        $categoryId = $request->integer('category_id') ?: null;
        $size = $request->string('size')->toString();
        $search = $request->string('search')->trim()->toString();

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($size !== '' && in_array($size, $this->sizes, true)) {
            $query->where('size', $size);
        }

        if ($search !== '') {
            $query->where('name', 'like', '%'.$search.'%');
        }

        $costumes = $query->orderBy('name')->get();

        return view('customer.catalog', [
            'costumes' => $costumes,
            'categories' => Category::orderBy('name')->get(),
            'sizes' => $this->sizes,
            'filters' => [
                'category_id' => $categoryId,
                'size' => $size,
                'search' => $search,
            ],
        ]);
    }

    /**
     * Display a single costume detail page with the reservation form.
     */
    public function show(Costume $costume): View
    {
        $costume->load('category');

        return view('customer.show', [
            'costume' => $costume,
        ]);
    }
}
