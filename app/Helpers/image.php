<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

if(!function_exists('fakeImage')){
    /**
     * Generate a fake image and store it directly in storage/public.
     * Intended for Factory / Seeder usage.
     *
     * @param string $folder
     * @param int $width
     * @param int $height
     * @param string $disk
     * @return string Stored image path
     */
    function fakeImage(
        string $folder,
        string $type = 'product',
        int $width = 600,
        int $height = 600,
    ): string {
        $map = [
            'avatar' => 'person,face',
            'product' => 'clothing,fashion,shirt',
        ];

        $category = $map[$type] ?? 'fashion';

        $filename = Str::uuid() . '.jpg';
        $path = "{$folder}/{$filename}";

        $url = "https://loremflickr.com/{$width}/{$height}/{$category}";

        Storage::put($path, file_get_contents($url));

        return "storage/{$folder}/{$filename}";
    }
}
