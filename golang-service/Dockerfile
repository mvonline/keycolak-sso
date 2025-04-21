FROM golang:1.21-alpine

WORKDIR /app

# Install necessary build tools
RUN apk add --no-cache gcc musl-dev

# Copy go mod and sum files
COPY go.mod ./

# Download dependencies and generate go.sum
RUN go mod download && \
    go get github.com/gorilla/mux && \
    go get github.com/joho/godotenv && \
    go mod tidy

# Copy the source code
COPY . .

# Build the application
RUN go build -o main .

# Expose port 8081
EXPOSE 8081

# Command to run the executable
CMD ["./main"] 