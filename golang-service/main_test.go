package main

import (
	"encoding/json"
	"net/http"
	"net/http/httptest"
	"os"
	"testing"
)

func TestHealthEndpoint(t *testing.T) {
	req, err := http.NewRequest("GET", "/health", nil)
	if err != nil {
		t.Fatal(err)
	}

	rr := httptest.NewRecorder()
	handler := http.HandlerFunc(healthHandler)
	handler.ServeHTTP(rr, req)

	if status := rr.Code; status != http.StatusOK {
		t.Errorf("handler returned wrong status code: got %v want %v",
			status, http.StatusOK)
	}

	expected := map[string]string{"status": "healthy"}
	var response map[string]string
	err = json.Unmarshal(rr.Body.Bytes(), &response)
	if err != nil {
		t.Fatal(err)
	}

	if response["status"] != expected["status"] {
		t.Errorf("handler returned unexpected body: got %v want %v",
			response, expected)
	}
}

func TestProtectedEndpointWithoutToken(t *testing.T) {
	req, err := http.NewRequest("GET", "/api/protected", nil)
	if err != nil {
		t.Fatal(err)
	}

	rr := httptest.NewRecorder()
	handler := keycloakMiddleware(http.HandlerFunc(protectedHandler))
	handler.ServeHTTP(rr, req)

	if status := rr.Code; status != http.StatusUnauthorized {
		t.Errorf("handler returned wrong status code: got %v want %v",
			status, http.StatusUnauthorized)
	}
}

func TestProtectedEndpointWithValidToken(t *testing.T) {
	// Mock Keycloak server
	mockKeycloak := httptest.NewServer(http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		userInfo := UserInfo{
			Sub:               "123",
			Email:             "test@example.com",
			PreferredUsername: "testuser",
			RealmAccess: struct {
				Roles []string `json:"roles"`
			}{
				Roles: []string{"user"},
			},
		}
		json.NewEncoder(w).Encode(userInfo)
	}))
	defer mockKeycloak.Close()

	// Set environment variables for testing
	os.Setenv("KEYCLOAK_URL", mockKeycloak.URL)
	os.Setenv("KEYCLOAK_REALM", "master")

	req, err := http.NewRequest("GET", "/api/protected", nil)
	if err != nil {
		t.Fatal(err)
	}
	req.Header.Set("Authorization", "Bearer valid-token")

	rr := httptest.NewRecorder()
	handler := keycloakMiddleware(http.HandlerFunc(protectedHandler))
	handler.ServeHTTP(rr, req)

	if status := rr.Code; status != http.StatusOK {
		t.Errorf("handler returned wrong status code: got %v want %v",
			status, http.StatusOK)
	}

	var response map[string]interface{}
	err = json.Unmarshal(rr.Body.Bytes(), &response)
	if err != nil {
		t.Fatal(err)
	}

	if response["message"] != "This is a protected endpoint" {
		t.Errorf("handler returned unexpected message: got %v want %v",
			response["message"], "This is a protected endpoint")
	}

	if response["service"] != "Golang Service" {
		t.Errorf("handler returned unexpected service: got %v want %v",
			response["service"], "Golang Service")
	}
}

func TestProtectedEndpointWithInvalidToken(t *testing.T) {
	// Mock Keycloak server that returns error
	mockKeycloak := httptest.NewServer(http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		w.WriteHeader(http.StatusUnauthorized)
	}))
	defer mockKeycloak.Close()

	// Set environment variables for testing
	os.Setenv("KEYCLOAK_URL", mockKeycloak.URL)
	os.Setenv("KEYCLOAK_REALM", "master")

	req, err := http.NewRequest("GET", "/api/protected", nil)
	if err != nil {
		t.Fatal(err)
	}
	req.Header.Set("Authorization", "Bearer invalid-token")

	rr := httptest.NewRecorder()
	handler := keycloakMiddleware(http.HandlerFunc(protectedHandler))
	handler.ServeHTTP(rr, req)

	if status := rr.Code; status != http.StatusUnauthorized {
		t.Errorf("handler returned wrong status code: got %v want %v",
			status, http.StatusUnauthorized)
	}
} 