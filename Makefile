.PHONY: up down logs shell test stan cs-fix precommit

up:
	docker build -t lsm-slim-app .
	-docker rm -f lsm-slim-app-container
	docker run -d --name lsm-slim-app-container -p 8000:8000 -v $(PWD):/app lsm-slim-app

down:
	-docker stop lsm-slim-app-container
	-docker rm lsm-slim-app-container

logs:
	docker logs -f lsm-slim-app-container

shell:
	docker exec -it lsm-slim-app-container bash

test:
	docker run --rm -v $(PWD):/app lsm-slim-app vendor/bin/phpunit

stan:
	docker run --rm -v $(PWD):/app lsm-slim-app vendor/bin/phpstan analyse src tests

cs-fix:
	docker run --rm -v $(PWD):/app lsm-slim-app vendor/bin/php-cs-fixer fix src --rules=@PSR12
	docker run --rm -v $(PWD):/app lsm-slim-app vendor/bin/php-cs-fixer fix tests --rules=@PSR12

precommit:
	$(MAKE) cs-fix
	$(MAKE) stan
	$(MAKE) test
