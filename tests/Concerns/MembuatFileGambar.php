<?php

namespace Tests\Concerns;

use Illuminate\Http\UploadedFile;

/**
 * Membuat file gambar palsu untuk keperluan test.
 *
 * Sengaja tidak memakai UploadedFile::fake()->image() karena fitur itu
 * membutuhkan ekstensi GD, yang belum tentu terpasang di semua environment.
 * Sebagai gantinya dipakai file PNG 1x1 yang valid.
 */
trait MembuatFileGambar
{
    /**
     * Isi PNG 1x1 pixel yang valid.
     */
    private const PNG_1X1 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==';

    /**
     * File gambar PNG 1x1.
     */
    protected function fileGambar(string $nama = 'jalan.png'): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            $nama,
            (string) base64_decode(self::PNG_1X1, true)
        );
    }

    /**
     * File gambar PNG dengan ukuran tertentu dalam kilobyte.
     */
    protected function fileGambarSebesar(
        int $kilobyte,
        string $nama = 'jalan-besar.png'
    ): UploadedFile {
        $isi = (string) base64_decode(self::PNG_1X1, true);

        return UploadedFile::fake()->createWithContent(
            $nama,
            $isi . str_repeat("\0", max(0, $kilobyte * 1024 - strlen($isi)))
        );
    }
}
