# Laravel Tinx
Inject cool stuff into laravels tinker

<img src="https://i.imgur.com/tCmU1CF.gif" title="source: imgur.com" />

## Installation

    composer require ajthinking/tinx
Thats it. This package supports Laravel [Package Discovery](https://laravel.com/docs/5.5/packages#package-discovery).

## Usage
    php artisan tinx

### Reload your tinker session
    re() 
This will allow you to immediatly test out your changes.

### Magic models

Tinx sniffs your models and prepare the following shortcuts.

| Example usage        | Equals           |
| ------------- |:-------------:|
| ```$u```      | ```App\User::first()``` |
| ```$c```      | ```App\Models\Car::first()``` |
| ```u(3)```      | ```App\User::find(3)``` |
| ```u("gmail")```      | ```Where "gmail" is found in any column.``` |
| ```u()```      | ```"App\User"``` |
| ```u()::where(...)```      | ```App\User::where(...)``` |

The naming conventions are decided by a strategy function, for instance "shortestUnique".
Lets say you have two models ```Car``` and ```Crocodile```. Tinx will then prepare the following variables and functions: ```$ca```, ```ca()```, ```$cr```, ```cr()```.

## Contributing
Please post issues and send PR:s. 

### Suggested improvment
* Guard against collision with PHP-built-in-functions and tinx specific re() and names()
* CamelCase naming strategy (CloudInstanceManager -> $cim )
* Add tests (this package is untested :/ )

## License
MIT