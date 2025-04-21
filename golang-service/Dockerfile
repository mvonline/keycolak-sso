FROM golang:1.21-alpine

WORKDIR /app

# Install necessary build tools
RUN apk add --no-cache gcc musl-dev

# Copy go mod file
COPY go.mod ./

# Download all dependencies and generate go.sum
RUN go mod download && go mod tidy

# Copy the source code
COPY . .

# Build the application
RUN go build -o main .

# Expose port 8081
EXPOSE 8081

# Command to run the executable
CMD ["./main"] 