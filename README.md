```bash
docker run  --rm -it -v "$(pwd):/app" -w /app -u 1000:1000 composer install
```

# run test
```bash
docker run  --rm -it -v "$(pwd):/app" -w /app -u 1000:1000 php:latest vendor/bin/phpunit


docker run  --rm -it -v "$(pwd):/app" -w /app -u 1000:1000 php:latest bin/console test data/cars.csv
```
