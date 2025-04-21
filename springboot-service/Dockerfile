FROM maven:3.8.4-openjdk-17-slim AS build

WORKDIR /app

# Copy the Maven files
COPY pom.xml .
COPY src ./src

# Build the application
RUN mvn clean package -DskipTests

# Create the runtime image
FROM openjdk:17-slim

WORKDIR /app

# Copy the built JAR from the build stage
COPY --from=build /app/target/*.jar app.jar

# Expose port 8082
EXPOSE 8082

# Command to run the application
ENTRYPOINT ["java", "-jar", "app.jar"] 