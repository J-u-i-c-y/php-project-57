<?php

namespace Database\Seeders;

use App\Models\Label;
use Illuminate\Database\Seeder;

class LabelSeeder extends Seeder
{
    public function run(): void
    {
        $labels = [
            ['name' => 'ошибка', 'description' => 'Какая-то ошибка в коде или в работе приложения'],
            ['name' => 'документация', 'description' => 'Задача, связанная с документацией'],
            ['name' => 'дубликат', 'description' => 'Повтор другой задачи'],
            ['name' => 'доработка', 'description' => 'Новая фича, которую нужно запилить'],
        ];

        foreach ($labels as $label) {
            Label::firstOrCreate(
                ['name' => $label['name']],
                $label
            );
        }
    }
}