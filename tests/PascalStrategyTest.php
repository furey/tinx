<?php

namespace Ajthinking\Tinx\Tests;

use Ajthinking\Tinx\Models\Model;
use Ajthinking\Tinx\Naming\StrategyFactory;
use PHPUnit\Framework\TestCase;

class PascalStrategyTest extends TestCase
{
    /**
     * @var bool
     * */
    private $debugging = false;

    /** @test */
    function it_generates_unique_names_for_a_large_number_of_random_models()
    {
        // Arrange…
        $number = 1000;
        $models = $this->generateRandomModels($number);

        // Act…
        $names = $this->getNamesFor($models);

        // Assert…
        $this->assertCount($number, $names);
    }

    /** @test */
    function short_class_names_translate_to_pascal_names()
    {
        // Arrange…
        $models = $this->getModelsFor([
            'App\Apple',
            'App\Orange',
            'App\FruitBowl',
            'App\BagOfGroceries',
            'App\BagOfGroceries\PlasticBag',
            'App\BagOfGroceries\Receipt',
        ]);

        // Act…
        $names = $this->getNamesFor($models);

        // Assert…
        $this->assertEquals([
            'App\Apple' => 'a',
            'App\Orange' => 'o',
            'App\FruitBowl' => 'fb',
            'App\BagOfGroceries' => 'bog',
            'App\BagOfGroceries\PlasticBag' => 'pb',
            'App\BagOfGroceries\Receipt' => 'r',
        ], $names);
    }

    /** @test */
    function short_class_names_with_numbers_translate_to_pascal_names()
    {
        // Arrange…
        $models = $this->getModelsFor([
            'App\Apple1',
            'App\Apple123',
            'App\Apple123456',
        ]);

        // Act…
        $names = $this->getNamesFor($models);

        // Assert…
        $this->assertEquals([
            'App\Apple1' => 'a1',
            'App\Apple123' => 'a12',
            'App\Apple123456' => 'a123',
        ], $names);
    }

    /** @test */
    function same_pascal_case_characters_translate_to_unique_pascal_names()
    {
        // Arrange…
        $models = $this->getModelsFor([
            'App\Apple', // 'a'
            'App\Apricot', // 'a'
            'App\Avocado', // 'a'
            'App\PawPaw', // 'pp'
            'App\PawPear', // 'pp'
            'App\PricklyPear', // 'pp'
        ]);

        // Act…
        $names = $this->getNamesFor($models);

        // Assert…
        $this->assertEquals([
            'App\Apple' => 'a',
            'App\Apricot' => 'ap',
            'App\Avocado' => 'av',
            'App\PawPaw' => 'pp',
            'App\PawPear' => 'ppe',
            'App\PricklyPear' => 'prp',
        ], $names);
    }

    /** @test */
    function same_pascal_case_characters_with_different_namespaces_translate_to_unique_pascal_names()
    {
        // Arrange…
        $models = $this->getModelsFor([
            'App\Apple',
            'App\BagOfGroceries\Apple',
            'App\FruitBowl\Apple',
        ]);

        // Act…
        $names = $this->getNamesFor($models);

        // Assert…
        $this->assertEquals([
            'App\Apple' => 'a',
            'App\BagOfGroceries\Apple' => 'ap',
            'App\FruitBowl\Apple' => 'app',
        ], $names);
    }

    /** @test */
    function same_short_class_names_with_different_namespaces_translate_to_unique_pascal_names_eventually_including_namespace_prefix()
    {
        // Arrange…
        $models = $this->getModelsFor([
            'App\App',
            'App\Nested\App',
            'App\Nested\Nested\App',
            'App\Nested\Nested\Nested\App',
            'App\Nested\Nested\Nested\Nested\App',
            'App\Nested\Nested\Nested\Tested\App',
        ]);

        // Act…
        $names = $this->getNamesFor($models);

        // Assert…
        $this->assertEquals([
            'App\App' => 'a',
            'App\Nested\App' => 'ap',
            'App\Nested\Nested\App' => 'app',
            'App\Nested\Nested\Nested\App' => 'na',
            'App\Nested\Nested\Nested\Nested\App' => 'nna',
            'App\Nested\Nested\Nested\Tested\App' => 'nta',
        ], $names);
    }

    /** @test */
    function same_short_class_names_with_different_numeric_namespaces_translate_to_unique_pascal_names()
    {
        // Arrange…
        $models = $this->getModelsFor([
            'App\A1\A',
            'App\A2\A',
            'App\A3\A',
            'App\A4\A',
        ]);

        // Act…
        $names = $this->getNamesFor($models);

        // Assert…
        $this->assertEquals([
            'App\A1\A' => 'a',
            'App\A2\A' => 'a2a',
            'App\A3\A' => 'a3a',
            'App\A4\A' => 'a4a',
        ], $names);
    }

    /** @test */
    function reserved_keywords_translate_to_unique_non_forbidden_pascal_names()
    {
        // Arrange…
        $models = $this->getModelsFor([
            'App\AppleNectarineDate', // 'and'
            'App\AppleStrawberry', // 'as'
            'App\DateOrange', // 'do'
            'App\FigOrangeRambutan', // 'for',
            // etc…
        ]);

        // Act…
        $names = $this->getNamesFor($models);

        // Assert…
        $this->assertEquals([
            'App\AppleNectarineDate' => 'anda',
            'App\AppleStrawberry' => 'ast',
            'App\DateOrange' => 'dor',
            'App\FigOrangeRambutan' => 'fora',
        ], $names);
    }

    /** @test */
    function same_reserved_keywords_translate_to_unique_non_forbidden_pascal_names()
    {
        // Arrange…
        $models = $this->getModelsFor([
            'App\AppleNectarineDate', // 'and'
            'App\ApricotNashiDewberry', // 'and'
            'App\Nested\ApricotNashiDewberry', // 'and'
        ]);

        // Act…
        $names = $this->getNamesFor($models);

        // Assert…
        $this->assertEquals([
            'App\AppleNectarineDate' => 'anda',
            'App\ApricotNashiDewberry' => 'ande',
            'App\Nested\ApricotNashiDewberry' => 'andew',
        ], $names);
    }

    /**
     * @param array[string] $classNames
     * @return \Illuminate\Support\Collection
     * */
    private function getModelsFor($classNames)
    {
        return collect($classNames)->map(function ($className) {
            return new Model($className);
        });
    }

    /**
     * @param int $total
     * @return \Illuminate\Support\Collection
     * */
    private function generateRandomModels($total)
    {
        $models = [];

        for ($i = 0; $i < $total; $i++) {
            do {
                $className = $this->generateRandomClassName();
            } while (isset($models[$className]));
            $models[$className] = new Model($className);
        }

        return collect($models)->values();
    }

    /**
     * @return string
     * */
    private function generateRandomClassName()
    {
        $segments = [];
        $totalSegments = rand(1, 5);
        
        $words = collect(['Apple', 'Banana', 'Carrot', 'Date', 'Elderberry']);
        $totalWords = count($words);
        
        for ($i = 0; $i < $totalSegments; $i++) {
            $segmentWords = $words->shuffle()->random(rand(1, $totalWords))->toArray();
            $segments[] = array_reduce($segmentWords, function ($carry, $word) {
                return $carry . $word . (rand(0, 5) === 0 ? rand(1, 999) : '');
            });
        }

        return implode('\\', $segments);
    }

    /**
     * @param \Illuminate\Support\Collection $models
     * @return array[string]
     * */
    private function getNamesFor($models)
    {
        $names = StrategyFactory::make('pascal', $models)->getNames();

        $this->dump(compact('names'));

        return $names;
    }

    /**
     * @param array $args
     * @return void
     * */
    private function dump(...$args)
    {
        if ($this->debugging) {
            dump(...$args);
        }
    }
}
