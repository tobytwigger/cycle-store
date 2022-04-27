<?php

namespace App\Services\Analysis\Parser;

use App\Models\File;
use App\Services\Analysis\Analyser\Analysis;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Parser parser(string $type) Get the file parser for the given file extension
 * @method static void registerCustomParser(string $type, \Closure $creator) Register a function to create a new parser for the given type
 * @method static Analysis parse(File $file) Read the given file
 */
class Parser extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ParserFactoryContract::class;
    }
}
