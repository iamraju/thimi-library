<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LibraryCatalogSeeder extends Seeder
{
    private const PALETTE = ['#1e3a5f', '#7b2d26', '#2f5d3a', '#5b3a73', '#8a5a14', '#1f5f66', '#4a4a4a'];

    public function run(): void
    {
        $categories = [];
        foreach ([
            'Technology' => 'Computing, science and engineering',
            'Literature' => 'Literary criticism and history of literature',
            'Novel' => 'Fiction and novels',
            'Poem' => 'Poetry and epics',
            'Auto Biography' => 'Autobiographies and memoirs',
            'Culture' => 'Culture, festivals and traditions',
            'History' => 'History of Nepal and the region',
            'Magazine' => 'Periodical magazines',
            'Newspaper' => 'Daily and weekly newspapers',
            'Children' => 'Children stories and rhymes',
            'Religion' => 'Religious and philosophical texts',
        ] as $title => $description) {
            $categories[$title] = Category::updateOrCreate(['title' => $title], ['description' => $description, 'status' => 1]);
        }

        $publishers = [];
        foreach ([
            'Sajha Prakashan' => 'Pulchowk, Lalitpur',
            'Ratna Pustak Bhandar' => 'Bhotahity, Kathmandu',
            'Nepal Academy' => 'Kamaladi, Kathmandu',
            'Kantipur Publications' => 'Kantipur Complex, Subidhanagar, Kathmandu',
            'FinePrint Books' => 'Jhamsikhel, Lalitpur',
            'Gorkhapatra Sansthan' => 'Dharmapath, Kathmandu',
            'Madhuparka Prakashan' => 'Kathmandu',
        ] as $title => $address) {
            $publishers[$title] = Publisher::updateOrCreate(['title' => $title], ['address' => $address, 'status' => 1]);
        }

        // [title, author, category, publisher, year, price]
        $books = [
            ['Muna Madan', 'Laxmi Prasad Devkota', 'Poem', 'Sajha Prakashan', 1936, 150],
            ['Ramayan', 'Bhanubhakta Acharya', 'Poem', 'Nepal Academy', 1887, 200],
            ['Basain', 'Lil Bahadur Chhetri', 'Novel', 'Sajha Prakashan', 1957, 250],
            ['Seto Bagh', 'Diamond Shumsher Rana', 'Novel', 'Sajha Prakashan', 1973, 300],
            ['Shirishko Phool', 'Parijat', 'Novel', 'Sajha Prakashan', 1965, 280],
            ['Karnali Blues', 'Buddhisagar', 'Novel', 'FinePrint Books', 2010, 350],
            ['Palpasa Cafe', 'Narayan Wagle', 'Novel', 'FinePrint Books', 2005, 400],
            ['Atmabritanta', 'Bishweshwar Prasad Koirala', 'Auto Biography', 'Ratna Pustak Bhandar', 1998, 450],
            ['Nepali Sahityako Itihas', 'Taranath Sharma', 'Literature', 'Ratna Pustak Bhandar', 1978, 500],
            ['Nepalko Sankshipta Itihas', 'Baburam Acharya', 'History', 'Nepal Academy', 1969, 380],
            ['Nepali Sanskriti', 'Sample Author', 'Culture', 'Nepal Academy', 2015, 320],
            ['Computer Shiksha', 'Sample Author', 'Technology', 'Ratna Pustak Bhandar', 2020, 550],
            ['Programming Basics in Nepali', 'Sample Author', 'Technology', 'FinePrint Books', 2022, 600],
            ['Madhuparka', 'Madhuparka Editorial Team', 'Magazine', 'Madhuparka Prakashan', 2024, 100],
            ['Himal Khabarpatrika', 'Himal Editorial Team', 'Magazine', 'Kantipur Publications', 2024, 120],
            ['Kantipur Daily', 'Kantipur Editorial Team', 'Newspaper', 'Kantipur Publications', 2024, 10],
            ['Gorkhapatra', 'Gorkhapatra Editorial Team', 'Newspaper', 'Gorkhapatra Sansthan', 2024, 10],
            ['Bhagavad Gita (Nepali)', 'Sample Translator', 'Religion', 'Ratna Pustak Bhandar', 2012, 220],
        ];

        foreach ($books as $i => [$title, $author, $category, $publisher, $year, $price]) {
            $slug = Str::slug($title);
            $path = $this->makeCover($slug, $title, $author, self::PALETTE[$i % count(self::PALETTE)]);

            Book::updateOrCreate(['slug' => $slug], [
                'title' => $title,
                'category_id' => $categories[$category]->id,
                'publisher_id' => $publishers[$publisher]->id,
                'book_type' => 'new',
                'cover_image' => $path,
                'author_name' => $author,
                'written_by' => $author,
                'publish_date' => "{$year}-01-01",
                'edition' => '1st',
                'price' => $price,
                'regd_date' => now()->toDateString(),
                'remarks' => 'Sample data',
                'status' => 1,
            ]);
        }
    }

    /** Generates a placeholder SVG cover; real covers are copyrighted and can be uploaded via the UI. */
    private function makeCover(string $slug, string $title, string $author, string $color): string
    {
        $t = e($title);
        $a = e($author);
        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="300" height="450" viewBox="0 0 300 450">
<rect width="300" height="450" fill="{$color}"/>
<rect x="15" y="15" width="270" height="420" fill="none" stroke="#ffffff" stroke-opacity="0.6" stroke-width="2"/>
<text x="150" y="200" fill="#ffffff" font-family="sans-serif" font-size="26" font-weight="bold" text-anchor="middle">{$t}</text>
<text x="150" y="380" fill="#ffffff" fill-opacity="0.85" font-family="sans-serif" font-size="16" text-anchor="middle">{$a}</text>
</svg>
SVG;

        $path = "covers/seed/{$slug}.svg";
        Storage::disk('public')->put($path, $svg);

        return $path;
    }
}
