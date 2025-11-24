.PHONY: up down logs shell

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
