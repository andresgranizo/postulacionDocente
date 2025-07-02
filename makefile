# Variables
SERVICE_NAME=app
DOCKER_COMPOSE=docker-compose

# Construir la imagen (si tienes Dockerfile)
build:
	@echo "🔧 Construyendo imagen..."
	$(DOCKER_COMPOSE) build

# Levantar servicios
up:
	@echo "🚀 Levantando contenedores..."
	$(DOCKER_COMPOSE) up -d

# Detener servicios
down:
	@echo "🛑 Deteniendo contenedores..."
	$(DOCKER_COMPOSE) down

# Ver estado
status:
	$(DOCKER_COMPOSE) ps

# Instalar dependencias de Composer
composer-install:
	@echo "📦 Instalando dependencias Composer..."
	$(DOCKER_COMPOSE) run --rm $(SERVICE_NAME) composer install

# Crear migración
migrate-make:
	@read -p "Nombre de la migración: " name; \
	$(DOCKER_COMPOSE) exec $(SERVICE_NAME) php artisan make:migration $$name

# Ejecutar migraciones
migrate:
	@echo "📂 Ejecutando migraciones..."
	$(DOCKER_COMPOSE) exec $(SERVICE_NAME) php artisan migrate

# Rollback de migraciones
migrate-rollback:
	@echo "↩️ Haciendo rollback..."
	$(DOCKER_COMPOSE) exec $(SERVICE_NAME) php artisan migrate:rollback

# Crear modelo
make-model:
	@read -p "Nombre del modelo: " name; \
	$(DOCKER_COMPOSE) exec $(SERVICE_NAME) php artisan make:model $$name

# Crear controlador
make-controller:
	@read -p "Nombre del controlador: " name; \
	$(DOCKER_COMPOSE) exec $(SERVICE_NAME) php artisan make:controller $$name

# Mostrar logs en tiempo real
logs:
	$(DOCKER_COMPOSE) logs -f

# Acceso bash
bash:
	$(DOCKER_COMPOSE) exec $(SERVICE_NAME) bash

# Ayuda
help:
	@echo "🛠️  Comandos disponibles:"
	@echo "  make build               Construir imagen"
	@echo "  make up                  Levantar contenedores"
	@echo "  make down                Detener contenedores"
	@echo "  make status              Ver estado"
	@echo "  make composer-install    Instalar dependencias Composer"
	@echo "  make migrate-make        Crear migración (te pide nombre)"
	@echo "  make migrate             Ejecutar migraciones"
	@echo "  make migrate-rollback    Rollback de migraciones"
	@echo "  make make-model          Crear modelo (te pide nombre)"
	@echo "  make make-controller     Crear controlador (te pide nombre)"
	@echo "  make logs                Ver logs en tiempo real"
	@echo "  make bash                Acceder al bash del contenedor"
