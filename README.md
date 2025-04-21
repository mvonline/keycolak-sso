# SSO Microservices Environment

This project demonstrates a multi-service backend environment with Single Sign-On (SSO) integration using Keycloak. The environment includes Laravel, Golang, and Spring Boot services, all secured with OAuth2/OpenID Connect.

## Prerequisites

- Docker and Docker Compose
- Make (optional, for using Makefile commands)
- Postman (for testing APIs)

## Project Structure

```
.
├── laravel-service/     # Laravel PHP service
├── golang-service/      # Golang service
├── springboot-service/  # Spring Boot service
├── docs/               # Documentation
└── docker-compose.yml  # Docker Compose configuration
```

## Setup Instructions

1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd <repository-name>
   ```

2. Create environment files:
   ```bash
   cp .env.example .env
   ```

3. Generate client secrets for each service:
   ```bash
   # These will be used in the .env file
   LARAVEL_CLIENT_SECRET=$(openssl rand -base64 32)
   GOLANG_CLIENT_SECRET=$(openssl rand -base64 32)
   SPRINGBOOT_CLIENT_SECRET=$(openssl rand -base64 32)
   ```

4. Start the services:
   ```bash
   docker-compose up -d
   ```

5. Access Keycloak Admin Console:
   - URL: http://localhost:8080
   - Username: admin
   - Password: admin

6. Import the Keycloak realm configuration:
   - Log in to the Keycloak Admin Console
   - Go to Master realm
   - Click "Import" and select the `keycloak-realm-export.json` file

## Service Endpoints

### Laravel Service
- Base URL: http://localhost:8000
- Protected endpoint: `/api/protected`

### Golang Service
- Base URL: http://localhost:8081
- Protected endpoint: `/api/protected`

### Spring Boot Service
- Base URL: http://localhost:8082
- Protected endpoint: `/api/protected`

## Authentication Flow

1. Obtain an access token:
   ```bash
   curl -X POST http://localhost:8080/realms/master/protocol/openid-connect/token \
     -d "client_id=<client-id>" \
     -d "client_secret=<client-secret>" \
     -d "grant_type=client_credentials"
   ```

2. Use the token to access protected endpoints:
   ```bash
   curl -H "Authorization: Bearer <access-token>" \
     http://localhost:8000/api/protected
   ```

## Testing

1. Import the Postman collection from `docs/postman/sso-microservices.postman_collection.json`
2. Update the environment variables in Postman with your client secrets
3. Run the collection to test all endpoints

## Development

Each service has its own development environment and can be run independently:

### Laravel Service
```bash
cd laravel-service
composer install
php artisan serve
```

### Golang Service
```bash
cd golang-service
go mod download
go run main.go
```

### Spring Boot Service
```bash
cd springboot-service
./mvnw spring-boot:run
```

## Security Considerations

- All sensitive information is managed through environment variables
- Client secrets are generated securely and stored in .env files
- Services validate tokens on every request
- Keycloak is configured with secure defaults

## Troubleshooting

1. Check service logs:
   ```bash
   docker-compose logs <service-name>
   ```

2. Verify Keycloak connectivity:
   ```bash
   curl http://localhost:8080/health
   ```

3. Check service health endpoints:
   - Laravel: http://localhost:8000/health
   - Golang: http://localhost:8081/health
   - Spring Boot: http://localhost:8082/actuator/health

## Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details. 