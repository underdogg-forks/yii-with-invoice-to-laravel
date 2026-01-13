<?php

namespace App\Http\Controllers;

use App\DTOs\ProductDTO;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {
        $this->middleware(['auth', 'permission:manage-products']);
    }

    public function index()
    {
        $products = $this->productService->getAll();
        
        $title = 'Products';
        $content = '';
        ob_start();
        ?>
        <h1>Products</h1>
        <a href="<?= route('products.create') ?>">Create New Product</a>
        
        <table>
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Family</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= htmlspecialchars($product->sku ?? '') ?></td>
                    <td><?= htmlspecialchars($product->name) ?></td>
                    <td><?= htmlspecialchars($product->price) ?></td>
                    <td><?= htmlspecialchars($product->family->name ?? '') ?></td>
                    <td>
                        <a href="<?= route('products.edit', $product->id) ?>">Edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
        $content = ob_get_clean();
        include __DIR__ . '/../../../resources/views/layout.php';
    }

    public function create()
    {
        $title = 'Create Product';
        $content = '';
        ob_start();
        ?>
        <h1>Create Product</h1>
        <form method="POST" action="<?= route('products.store') ?>">
            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
            
            <label>Name:</label>
            <input type="text" name="name" required>
            
            <label>SKU:</label>
            <input type="text" name="sku">
            
            <label>Description:</label>
            <textarea name="description"></textarea>
            
            <label>Price:</label>
            <input type="number" step="0.01" name="price" required>
            
            <button type="submit">Create Product</button>
        </form>
        <?php
        $content = ob_get_clean();
        include __DIR__ . '/../../../resources/views/layout.php';
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'family_id' => 'nullable|exists:product_families,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'price' => 'required|numeric|min:0',
        ]);

        $dto = new ProductDTO(...$validated);
        $this->productService->create($dto);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully');
    }

    public function edit(int $id)
    {
        $product = $this->productService->findById($id);
        
        $title = 'Edit Product';
        $content = '';
        ob_start();
        ?>
        <h1>Edit Product</h1>
        <form method="POST" action="<?= route('products.update', $product->id) ?>">
            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="_method" value="PUT">
            
            <label>Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($product->name) ?>" required>
            
            <label>SKU:</label>
            <input type="text" name="sku" value="<?= htmlspecialchars($product->sku ?? '') ?>">
            
            <label>Description:</label>
            <textarea name="description"><?= htmlspecialchars($product->description ?? '') ?></textarea>
            
            <label>Price:</label>
            <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($product->price) ?>" required>
            
            <button type="submit">Update Product</button>
        </form>
        <?php
        $content = ob_get_clean();
        include __DIR__ . '/../../../resources/views/layout.php';
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'family_id' => 'nullable|exists:product_families,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $id,
            'price' => 'required|numeric|min:0',
        ]);

        $validated['id'] = $id;
        $dto = new ProductDTO(...$validated);
        $this->productService->update($dto);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->productService->delete($id);

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully');
    }
}
