<?php

namespace App\Services\Translator;

interface Translator
{
    public function translate(string $text, string $from, string $to): string;
}
