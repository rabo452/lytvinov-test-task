# lytvinov-test-task

# Weather Service Docker Setup

## Prerequisites

1. **Install Docker and Docker Compose**:
   - Follow the official documentation to install Docker and Docker Compose:
     - [Install Docker](https://docs.docker.com/get-docker/)
     - [Install Docker Compose](https://docs.docker.com/compose/install/)

## Steps to Run

1. **Run the following command** to start the services:
   ```bash
   docker-compose up --build
   ```

   This will build and start the `php-fpm` and `nginx` containers.

2. **Access the application**:  
   Open your browser and go to `http://localhost:80`.

## Running Tests in the Container

1. **Execute tests inside the container**:
   To run your PHPUnit tests, use the following command to execute them within the `php-fpm` container:
   ```bash
   docker-compose exec php-fpm php bin/phpunit
   ```

   This will run the tests inside the running container.

2. **To run specific tests**:
   If you want to run tests from a specific file, use:
   ```bash
   docker-compose exec php-fpm php bin/phpunit tests/Service/WeatherServiceTest.php
   ```

   Replace `tests/Service/WeatherServiceTest.php` with the path to the test file you want to run.

---

That's it! If you need to stop the services, simply run:

```bash
docker-compose down
```

---

This should get your application running and your tests executed inside the Docker container!