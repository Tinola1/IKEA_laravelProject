<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ProductsTemplateExport implements FromArray, WithHeadings, WithEvents
{
    public function headings(): array
    {
        return ['name', 'description', 'price', 'stock', 'category'];
    }

    public function array(): array
    {
        return [
            ['BILLY Bookcase', 'Adjustable shelves, 80x28x202 cm', '4999.00', '10', 'Chairs'],
            ['KALLAX Shelf Unit', 'Can be used as a room divider', '3499.00', '5', 'Kitchen & Dining'],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $categories = Category::orderBy('name')->pluck('name')->toArray();
                $list = implode(',', $categories);

                // Apply dropdown to column E (category) rows 2–100
                for ($row = 2; $row <= 100; $row++) {
                    $validation = $sheet->getCell("E{$row}")->getDataValidation();
                    $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                    $validation->setAllowBlank(false);
                    $validation->setShowDropDown(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setErrorTitle('Invalid category');
                    $validation->setError('Please select a category from the dropdown list.');
                    $validation->setFormula1('"' . $list . '"');
                }

                // Bold the heading row
                $sheet->getStyle('A1:E1')->getFont()->setBold(true);

                // Auto-width columns
                foreach (range('A', 'E') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            }
        ];
    }
}