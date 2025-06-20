# php-api-server

A simple API server made for PHP 8 or above.

## Get started

1. Run `composer install` to install dependencies (has `phpmailer` though), and pouplate `.env`.
2. Create a file (eg. `file-name.php`) in `/src/api` folder, write some code, like this:

```PHP
<?php

return new Response(true, "Working");

```

3. Start docker `docker compose up -d` and PHP `php -S localhost:6969`, and use endpoint like this `localhost:6969/api/file-name`.

## Features

- `Response` class, return JSON formatted response
- `DB` class, prepared queries & elegant functions
- Native `.env` support
- `zod` class, for schema validation
- JS like `fetch` function (yea.. no stupid cURL)
- Some useful utils, like `GetIP`, `RandomNumber`, `RandomString` (like nanoid), `UploadFile`, `SendMail` etc.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for more details.

## Author

- **mohiwalla**
