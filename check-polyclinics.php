<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Polyclinic;

echo "Polyclinics in database:\n";
echo "------------------------\n";

foreach (Polyclinic::all() as $p) {
    echo "Code: {$p->code} - Name: {$p->name}\n";
}
