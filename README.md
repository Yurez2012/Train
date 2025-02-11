Run migration
```bash
docker compose exec -t app npm run build
```

Run migration
```bash
docker compose exec -t app php artisan migrate
```

Go to container
```bash
docker compose exec -it app sh
```
