# Docker + Composer template for Drupal projects

This is a local development stack for Drupal projects using docker.

This is based on  [drupal-composer/drupal-project](https://github.com/drupal-composer/drupal-project).

## How to use this
It's easier if you have [pihvi](https://github.com/pihvi-io/pihvi) development helpers installed first.

## Special modifications
* `web/sites` is moved away from web root to this root folder for easier git version control and composer usage. Running `$ composer install` will automatically symlink it to the right path.

## How to start developing
```
# Rename your site site.test->my-site.test
$ sed -i ‘s/site.test/my-site.test/’ docker-compose.yml

# Install composer packages
$ make install

# Start docker services
$ make start
```

## Maintainers
[Onni Hakala](https://github.com/onnimonni)

## License
GPLv2