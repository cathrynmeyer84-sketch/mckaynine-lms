<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use App\Models\Resource;
use Illuminate\Database\Seeder;

class ProductionContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedEmailTemplates();
        $this->seedResources();
    }

    private function seedEmailTemplates(): void
    {
        $path = database_path('seeders/data/email_templates.json');
        if (! file_exists($path)) {
            $this->command->warn('Email templates data file not found.');
            return;
        }

        $templates = json_decode(file_get_contents($path), true);
        foreach ($templates as $t) {
            EmailTemplate::updateOrCreate(['key' => $t['key']], $t);
        }

        $this->command->info('Email templates: ' . count($templates) . ' seeded.');
    }

    private function seedResources(): void
    {
        $path = database_path('seeders/data/resources.json');
        if (! file_exists($path)) {
            $this->command->warn('Resources data file not found.');
            return;
        }

        $resources = json_decode(file_get_contents($path), true);

        // Only seed if table is empty to avoid duplicates
        if (Resource::count() > 0) {
            $this->command->warn('Resources table already has data — skipping.');
            return;
        }

        foreach ($resources as $r) {
            Resource::create($r);
        }

        $this->command->info('Resources: ' . count($resources) . ' seeded.');
    }
}
