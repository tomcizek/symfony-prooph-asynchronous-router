## Running tests

To run tests execute *phpunit*:

  ```sh
  $ ./vendor/bin/phpunit
  ```

## Running PHPCodeSniffer

To check coding standards execute *phpcs*:

  ```sh
  $ ./vendor/bin/phpcs
  ```

To auto fix coding standard issues execute:

  ```sh
  $ ./vendor/bin/phpcbf
  ```
  
## Composer shortcuts

For every program above there are shortcuts defined in the `composer.json` file.

* `check`: Executes PHPCodeSniffer and PHPUnit
* `cs`: Executes PHPCodeSniffer
* `cs-fix`: Executes PHPCodeSniffer and auto fixes issues
* `test`: Executes PHPUnit
* `test-coverage`: Executes PHPUnit with code coverage
