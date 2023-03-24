Time Spent on Challenge:
24th: 1653 -> 1832

Comments:
- I ran composer update as this wouldnt play with PHP 8.2
- I wrote the tests first as an exercise in TDD/abstract thinking
- I removed sail from dev-dependencies, because Sail is just... nah.

Improvements I considered, related to refactoring and improving the command (but decided to keep it simpler...):
- User-specified number of retries, one per proxy in the list, if the first proxy fails
- User-specified timeout for the query

# iHASCO Technical Challenge

## Introduction

This package contains a single Artisan command which:

- Accepts a URL as an argument on the CLI.
- Queries an API to find an open proxy.
- Makes a request to the given URI via an open proxy.
- Outputs the returned HTTP headers to the console.
- Logs the request to a file.

Once installed, the command can be executed using (for example):

`php artisan query:url https://www.google.co.uk/`

## Installation

Follow the usual Composer install procedure.

## Objective

The existing code for this command has a number of problems:

- It's messy.
- It has no type information.
- It does not follow SOLID/OOP principles.
- There's no error (or "sad path") handling.
- There are no tests - because...
- It's not testable.

Your task is to refactor the code to resolve these issues - and then write your own tests (e.g. unit tests) to boot.

## Acceptance criteria

We're ultimately hoping to gauge your understanding of type safety, software testing, SOLID/OOP patterns and principles and such.

Our minimum expectations are that:

- There is a good test suite - and tests don't make any API calls or write to disk.
- There is a sensible level of error handling.
- Some attempt has been made to include type information using PHP's own type system as much as possible - only using PHPDoc as a last resort.

Feel free to include comments (either in the code itself or as a separate document) elaborating on your thought processes e.g. if there are any decisions that you want to explain, there's any ambiguity that you were unsure about, etc.
