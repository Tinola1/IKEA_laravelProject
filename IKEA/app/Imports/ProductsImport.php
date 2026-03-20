<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class ProductsImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public function model(array $row): ?Product
    {
        $category = Category::whereRaw('LOWER(name) = ?', [strtolower(trim($row['category']))])->first();

        if (!$category) {
            throw new \Exception("Row skipped: category \"{$row['category']}\" not found. Valid categories: Sofas & Armchairs, Beds & Mattresses, Tables & Desks, Chairs, Kitchen & Dining.");
        }

        return new Product([
            'category_id'  => $category->id,
            'name'         => trim($row['name']),
            'slug'         => Str::slug($row['name']) . '-' . Str::random(4),
            'description'  => trim($row['description'] ?? ''),
            'price'        => (float) $row['price'],
            'stock'        => (int)   ($row['stock'] ?? 0),
            'is_available' => 1,
        ]);
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'price'    => ['required', 'numeric', 'min:0'],
            'category' => ['required', 'string'],
            'stock'    => ['nullable', 'integer', 'min:0'],
        ];
    }
}