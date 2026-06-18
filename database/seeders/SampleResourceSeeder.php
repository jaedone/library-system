<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SampleResourceSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('publishers')->insert([
            ['publisher_key' => 'mit_press', 'publisher_name' => 'MIT Press', 'created_at' => $now, 'updated_at' => $now],
            ['publisher_key' => 'pearson_education', 'publisher_name' => 'Pearson Education', 'created_at' => $now, 'updated_at' => $now],
            ['publisher_key' => 'prentice_hall', 'publisher_name' => 'Prentice Hall', 'created_at' => $now, 'updated_at' => $now],
            ['publisher_key' => 'oreilly_media', 'publisher_name' => 'O\'Reilly Media', 'created_at' => $now, 'updated_at' => $now],
            ['publisher_key' => 'penguin_classics', 'publisher_name' => 'Penguin Classics', 'created_at' => $now, 'updated_at' => $now],
            ['publisher_key' => 'pup_publishing_house', 'publisher_name' => 'PUP Publishing House', 'created_at' => $now, 'updated_at' => $now],
            ['publisher_key' => 'oxford_university_press', 'publisher_name' => 'Oxford University Press', 'created_at' => $now, 'updated_at' => $now],
            ['publisher_key' => 'mcgraw_hill_education', 'publisher_name' => 'McGraw-Hill Education', 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('authors')->insert([
            ['author_key' => 'thomas_cormen', 'author_name' => 'Thomas H. Cormen', 'created_at' => $now, 'updated_at' => $now],
            ['author_key' => 'charles_leiserson', 'author_name' => 'Charles E. Leiserson', 'created_at' => $now, 'updated_at' => $now],
            ['author_key' => 'ronald_rivest', 'author_name' => 'Ronald L. Rivest', 'created_at' => $now, 'updated_at' => $now],
            ['author_key' => 'clifford_stein', 'author_name' => 'Clifford Stein', 'created_at' => $now, 'updated_at' => $now],
            ['author_key' => 'robert_martin', 'author_name' => 'Robert C. Martin', 'created_at' => $now, 'updated_at' => $now],
            ['author_key' => 'andrew_tanenbaum', 'author_name' => 'Andrew S. Tanenbaum', 'created_at' => $now, 'updated_at' => $now],
            ['author_key' => 'herbert_schildt', 'author_name' => 'Herbert Schildt', 'created_at' => $now, 'updated_at' => $now],
            ['author_key' => 'jose_rizal', 'author_name' => 'Jose Rizal', 'created_at' => $now, 'updated_at' => $now],
            ['author_key' => 'william_shakespeare', 'author_name' => 'William Shakespeare', 'created_at' => $now, 'updated_at' => $now],
            ['author_key' => 'donald_norman', 'author_name' => 'Donald A. Norman', 'created_at' => $now, 'updated_at' => $now],
            ['author_key' => 'pup_library_research_team', 'author_name' => 'PUP Library Research Team', 'created_at' => $now, 'updated_at' => $now],
        ]);

        $ids = [
            'book' => DB::table('material_types')->where('material_type_key', 'book')->value('id'),
            'thesis' => DB::table('material_types')->where('material_type_key', 'thesis')->value('id'),
            'journal' => DB::table('material_types')->where('material_type_key', 'journal')->value('id'),
            'ebook' => DB::table('material_types')->where('material_type_key', 'ebook')->value('id'),

            'cs' => DB::table('categories')->where('category_key', 'computer_science')->value('id'),
            'law' => DB::table('categories')->where('category_key', 'law')->value('id'),
            'literature' => DB::table('categories')->where('category_key', 'literature')->value('id'),
            'general' => DB::table('categories')->where('category_key', 'general_references')->value('id'),

            'available' => DB::table('copy_statuses')->where('status_key', 'available')->value('id'),
            'room_use' => DB::table('copy_statuses')->where('status_key', 'room_use_only')->value('id'),
            'digital' => DB::table('copy_statuses')->where('status_key', 'digital_access_only')->value('id'),

            'main' => DB::table('library_branches')->where('branch_code', 'main_library')->value('id'),
            'graduate' => DB::table('library_branches')->where('branch_code', 'graduate_library')->value('id'),
            'law_branch' => DB::table('library_branches')->where('branch_code', 'law_library')->value('id'),
            'digital_branch' => DB::table('library_branches')->where('branch_code', 'digital_library')->value('id'),
        ];

        $resources = [
            [
                'title' => 'Introduction to Algorithms',
                'isbn' => '9780262033848',
                'material_type_id' => $ids['book'],
                'category_id' => $ids['cs'],
                'publisher_key' => 'mit_press',
                'publication_year' => 2009,
                'edition' => '3rd Edition',
                'description' => 'A comprehensive textbook covering algorithms, data structures, and computational complexity.',
                'cover_image_path' => 'https://covers.openlibrary.org/b/isbn/9780262033848-L.jpg',
                'author_keys' => ['thomas_cormen', 'charles_leiserson', 'ronald_rivest', 'clifford_stein'],
                'branch_id' => $ids['main'],
                'copy_status_id' => $ids['available'],
                'copies' => 3,
                'shelf' => 'CS-A1',
                'is_reference_only' => false,
                'is_digital' => false,
            ],
            [
                'title' => 'Clean Code',
                'isbn' => '9780132350884',
                'material_type_id' => $ids['book'],
                'category_id' => $ids['cs'],
                'publisher_key' => 'pearson_education',
                'publication_year' => 2008,
                'edition' => '1st Edition',
                'description' => 'A practical guide to writing readable, maintainable, and professional software.',
                'cover_image_path' => 'https://covers.openlibrary.org/b/isbn/9780132350884-L.jpg',
                'author_keys' => ['robert_martin'],
                'branch_id' => $ids['main'],
                'copy_status_id' => $ids['available'],
                'copies' => 2,
                'shelf' => 'CS-B2',
                'is_reference_only' => false,
                'is_digital' => false,
            ],
            [
                'title' => 'Computer Networks',
                'isbn' => '9780132126953',
                'material_type_id' => $ids['book'],
                'category_id' => $ids['cs'],
                'publisher_key' => 'prentice_hall',
                'publication_year' => 2011,
                'edition' => '5th Edition',
                'description' => 'A foundational resource on computer networking concepts, protocols, and network architecture.',
                'cover_image_path' => 'https://covers.openlibrary.org/b/isbn/9780132126953-L.jpg',
                'author_keys' => ['andrew_tanenbaum'],
                'branch_id' => $ids['main'],
                'copy_status_id' => $ids['available'],
                'copies' => 2,
                'shelf' => 'CS-C3',
                'is_reference_only' => false,
                'is_digital' => false,
            ],
            [
                'title' => 'Java: The Complete Reference',
                'isbn' => '9781260440232',
                'material_type_id' => $ids['book'],
                'category_id' => $ids['cs'],
                'publisher_key' => 'mcgraw_hill_education',
                'publication_year' => 2018,
                'edition' => '11th Edition',
                'description' => 'A complete reference guide for Java programming language concepts and applications.',
                'cover_image_path' => 'https://covers.openlibrary.org/b/isbn/9781260440232-L.jpg',
                'author_keys' => ['herbert_schildt'],
                'branch_id' => $ids['main'],
                'copy_status_id' => $ids['available'],
                'copies' => 2,
                'shelf' => 'CS-D4',
                'is_reference_only' => false,
                'is_digital' => false,
            ],
            [
                'title' => 'Noli Me Tangere',
                'isbn' => '9780143039693',
                'material_type_id' => $ids['book'],
                'category_id' => $ids['literature'],
                'publisher_key' => 'penguin_classics',
                'publication_year' => 1887,
                'edition' => 'Classic Edition',
                'description' => 'A Philippine literary classic written by Dr. Jose Rizal.',
                'cover_image_path' => 'https://covers.openlibrary.org/b/isbn/9780143039693-L.jpg',
                'author_keys' => ['jose_rizal'],
                'branch_id' => $ids['main'],
                'copy_status_id' => $ids['available'],
                'copies' => 3,
                'shelf' => 'LIT-A1',
                'is_reference_only' => false,
                'is_digital' => false,
            ],
            [
                'title' => 'Hamlet',
                'isbn' => '9780141396507',
                'material_type_id' => $ids['book'],
                'category_id' => $ids['literature'],
                'publisher_key' => 'penguin_classics',
                'publication_year' => 1603,
                'edition' => 'Classic Edition',
                'description' => 'A tragedy by William Shakespeare and one of the most influential works in English literature.',
                'cover_image_path' => 'https://covers.openlibrary.org/b/isbn/9780141396507-L.jpg',
                'author_keys' => ['william_shakespeare'],
                'branch_id' => $ids['main'],
                'copy_status_id' => $ids['available'],
                'copies' => 2,
                'shelf' => 'LIT-B2',
                'is_reference_only' => false,
                'is_digital' => false,
            ],
            [
                'title' => 'The Design of Everyday Things',
                'isbn' => '9780465050659',
                'material_type_id' => $ids['book'],
                'category_id' => $ids['cs'],
                'publisher_key' => 'mit_press',
                'publication_year' => 2013,
                'edition' => 'Revised Edition',
                'description' => 'A well-known book on design, usability, and human-centered product thinking.',
                'cover_image_path' => 'https://covers.openlibrary.org/b/isbn/9780465050659-L.jpg',
                'author_keys' => ['donald_norman'],
                'branch_id' => $ids['main'],
                'copy_status_id' => $ids['available'],
                'copies' => 1,
                'shelf' => 'GEN-D1',
                'is_reference_only' => false,
                'is_digital' => false,
            ],
            [
                'title' => 'PUP Library Research Guide',
                'isbn' => null,
                'material_type_id' => $ids['ebook'],
                'category_id' => $ids['general'],
                'publisher_key' => 'pup_publishing_house',
                'publication_year' => 2026,
                'edition' => 'Digital Edition',
                'description' => 'A digital guide for using library research tools, citations, references, and academic databases.',
                'cover_image_path' => 'https://covers.openlibrary.org/b/id/11153223-L.jpg',
                'author_keys' => ['pup_library_research_team'],
                'branch_id' => $ids['digital_branch'],
                'copy_status_id' => $ids['digital'],
                'copies' => 0,
                'shelf' => null,
                'is_reference_only' => false,
                'is_digital' => true,
                'digital_url' => 'https://www.pup.edu.ph/library/',
            ],
            [
                'title' => 'Sample Undergraduate Thesis in Computer Science',
                'isbn' => null,
                'material_type_id' => $ids['thesis'],
                'category_id' => $ids['cs'],
                'publisher_key' => 'pup_publishing_house',
                'publication_year' => 2025,
                'edition' => null,
                'description' => 'A sample thesis entry for catalog testing. Thesis materials are marked as room-use only.',
                'cover_image_path' => 'https://covers.openlibrary.org/b/id/10521270-L.jpg',
                'author_keys' => ['pup_library_research_team'],
                'branch_id' => $ids['graduate'],
                'copy_status_id' => $ids['room_use'],
                'copies' => 1,
                'shelf' => 'TH-CS-2025',
                'is_reference_only' => true,
                'is_digital' => false,
            ],
            [
                'title' => 'Sample Legal Research Journal',
                'isbn' => null,
                'material_type_id' => $ids['journal'],
                'category_id' => $ids['law'],
                'publisher_key' => 'pup_publishing_house',
                'publication_year' => 2024,
                'edition' => 'Volume 1',
                'description' => 'A sample law-related journal entry for testing the catalog and library location filters.',
                'cover_image_path' => 'https://covers.openlibrary.org/b/id/11148460-L.jpg',
                'author_keys' => ['pup_library_research_team'],
                'branch_id' => $ids['law_branch'],
                'copy_status_id' => $ids['room_use'],
                'copies' => 1,
                'shelf' => 'LAW-JRNL-01',
                'is_reference_only' => true,
                'is_digital' => false,
            ],
        ];

        foreach ($resources as $index => $resource) {
            $publisherId = DB::table('publishers')->where('publisher_key', $resource['publisher_key'])->value('id');

            $resourceId = DB::table('resources')->insertGetId([
                'material_type_id' => $resource['material_type_id'],
                'category_id' => $resource['category_id'],
                'publisher_id' => $publisherId,
                'title' => $resource['title'],
                'isbn' => $resource['isbn'],
                'publication_year' => $resource['publication_year'],
                'edition' => $resource['edition'],
                'description' => $resource['description'],
                'cover_image_path' => $resource['cover_image_path'],
                'is_reference_only' => $resource['is_reference_only'],
                'is_digital' => $resource['is_digital'],
                'digital_url' => $resource['digital_url'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($resource['author_keys'] as $authorKey) {
                $authorId = DB::table('authors')->where('author_key', $authorKey)->value('id');

                DB::table('resource_authors')->insert([
                    'resource_id' => $resourceId,
                    'author_id' => $authorId,
                ]);
            }

            for ($copyNumber = 1; $copyNumber <= $resource['copies']; $copyNumber++) {
                DB::table('resource_copies')->insert([
                    'resource_id' => $resourceId,
                    'branch_id' => $resource['branch_id'],
                    'copy_status_id' => $resource['copy_status_id'],
                    'accession_number' => 'ACC-' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT) . '-' . $copyNumber,
                    'barcode' => 'BAR-' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT) . '-' . $copyNumber,
                    'shelf_location' => $resource['shelf'],
                    'is_borrowable' => !$resource['is_reference_only'],
                    'copy_condition' => 'Good',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
