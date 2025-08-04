<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateProductSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:generate-slugs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate slugs for products that do not have one.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating slugs for products...');

        $productsToUpdate = Product::whereNull('slug')->get();

        if ($productsToUpdate->isEmpty()) {
            $this->info('All products already have a slug. Nothing to do.');
            return 0;
        }

        $progressBar = $this->output->createProgressBar($productsToUpdate->count());
        $progressBar->start();

        foreach ($productsToUpdate as $product) {
            $slug = Str::slug($product->name);
            $count = 1;
            $originalSlug = $slug;

            // Ensure slug is unique
            while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }

            $product->slug = $slug;
            $product->save();
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->info('\nSlug generation complete!');

        return 0;
    }
}
