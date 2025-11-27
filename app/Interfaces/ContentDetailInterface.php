<?php

namespace App\Interfaces;

interface ContentDetailInterface
{
    /**
     * Mengembalikan array detail spesifik dari konten (Material/Assignment).
     * Penerapan Polimorfisme via Interface.
     */
    public function getDetails(): array;
}