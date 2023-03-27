> If using Sail, prepend the commands appropriately as documented on laravel.com

## Installation
`composer install`

`cp .env.example .env`

## Usage
`php artisan query:url https://www.google.co.uk/`

## Testing
`php artisan test`

Comments:
- About 5hrs spent overall - I was enjoying the exercise.
- I ran composer update as this wouldnt play with PHP 8.2..
- I wrote the tests first as an exercise in TDD/abstract thinking
- I'm a bit rusty with PHPUnit integrated into Laravel; the last TDD i performed was using Behat on Symfony, so I took a little longer
on this to refresh my memory.
- I've attempted to demonstrate both knowledge of Laravel as well as abstract OOP principles and Unit testing in PHP.
- With that in mind, I kinda wish I'd gone with the `Http` facade, but again, trying to demonstrate use of DI for testable code.
- I got a bit stuck using the Validator facade when it came to testing, as a Unit test shouldn't really be bootstrapping the app, but the Facade needs this
- Validation messages could've been stronger - i didn't look into reading the error message bag and validation in laravel is very geared towards convenience on http routes
- I would have preferred to use async/promises after having spent some time on js but I was concerned this would eat up time while I figure it out and how to test it.
- I appreciate the comments in the code are sparse, but I should hope that the code is pretty readable and self-explanatory! I write comments when it's important to explain complex thought processes, or even the reason *why* a piece of code may be the way it is.

Limitations
- The console command will only try the first proxy in the list, and will not retry if it fails. I wanted to circle back around to this but I am out of time.
  - I hope this doesn't detract from the code and tests too much, as I am aware that the original code did this.
- No specifiable timeout
- I'd have liked to add a buildable query string based on options rather than the full URL for the proxy list API in the env file.
- Error handling is fairly robust on the service classes, but a bit minimal in the console command, which I wrote last after the services.

