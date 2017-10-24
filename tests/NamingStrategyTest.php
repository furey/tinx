<?php

namespace Ajthinking\Tinx\Tests;

use PHPUnit\Framework\TestCase;
use Ajthinking\Tinx\Model;
use Ajthinking\Tinx\NamingStrategy;

class NamingStrategyTest extends TestCase
{
    public $strategies;

    public function setUp()
    {
        $this->strategies = [
            "shortestUnique",
            // Add your strategy here
        ];
    }

    /** @test */
    public function it_will_not_use_reserved_names_as_shortcuts()
    {
        $models = collect([
            // shortest unique = "min" (forbidden!)            
            new Model("App\Mindblower"),
            new Model("App\Microscope"),

            // PascalCase = "min" (forbidden!)
            new Model("App\MaximumInstanceModel")

            // Add more special cases here
            
            // Add ~1000 nouns from faker?
        ]);

        foreach($this->strategies as $strategy)
        {
            $names = NamingStrategy::$strategy($models);
            foreach($names as $name)
            {
                $this->assertTrue(!function_exists($name));
                $this->assertTrue(!in_array($name, NamingStrategy::forbiddenNames));
            }
        }
    }
}