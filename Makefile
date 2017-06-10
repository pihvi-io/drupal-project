.PHONY: default
default: install start
PROJECT_ROOT = /var/www/project
PWD = $(shell pwd)

# Alias for running command inside php docker container
run_docker = docker-compose run --no-deps --entrypoint="" --rm -v $(PWD):$(PROJECT_ROOT)

install:
	# Install composer packages
	$(run_docker) -T php composer install

update:
	# Update composer packages
	$(run_docker) -T php composer update

test:
	$(run_docker) -T php vendor/bin/codecept build
	$(run_docker) -T php vendor/bin/codecept run

shell:
	$(run_docker) php sh

reload:
	docker-compose up -d --force-recreate

start:
	docker-compose up -d

stop:
	docker-compose stop

destroy: stop
	docker-compose rm

logs:
	docker-compose logs