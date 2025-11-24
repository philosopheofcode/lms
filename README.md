# lms

## Prerequisites

- [Docker](https://www.docker.com/) is required to run this project.
- Make (optional, but recommended for using the Makefile).

## Getting Started

To start the project, run:

```bash
make up
```

This command will build the Docker image and start the container.
The application will be available at http://localhost:8000.

## Useful Commands

- Stop the application: `make down`
- View logs: `make logs`
- Access container shell: `make shell`
