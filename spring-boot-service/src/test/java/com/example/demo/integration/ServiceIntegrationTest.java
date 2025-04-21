package com.example.demo.integration;

import org.junit.jupiter.api.Test;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.test.autoconfigure.web.servlet.AutoConfigureMockMvc;
import org.springframework.boot.test.context.SpringBootTest;
import org.springframework.boot.test.mock.mockito.MockBean;
import org.springframework.http.MediaType;
import org.springframework.test.web.servlet.MockMvc;
import org.springframework.web.client.RestTemplate;

import static org.mockito.ArgumentMatchers.any;
import static org.mockito.ArgumentMatchers.eq;
import static org.mockito.Mockito.when;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.get;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.*;

@SpringBootTest
@AutoConfigureMockMvc
public class ServiceIntegrationTest {

    @Autowired
    private MockMvc mockMvc;

    @MockBean
    private RestTemplate restTemplate;

    @Test
    public void testServiceIntegrationWithValidToken() throws Exception {
        // Mock Keycloak response
        String mockUserInfo = "{\"sub\":\"123\",\"email\":\"test@example.com\",\"preferred_username\":\"testuser\",\"realm_access\":{\"roles\":[\"user\"]}}";
        when(restTemplate.getForObject(any(String.class), eq(String.class)))
            .thenReturn(mockUserInfo);

        mockMvc.perform(get("/api/protected")
                .header("Authorization", "Bearer valid-token")
                .contentType(MediaType.APPLICATION_JSON))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$.message").value("This is a protected endpoint"))
                .andExpect(jsonPath("$.service").value("Spring Boot Service"))
                .andExpect(jsonPath("$.user").exists());
    }

    @Test
    public void testServiceIntegrationWithInvalidToken() throws Exception {
        // Mock Keycloak error response
        when(restTemplate.getForObject(any(String.class), eq(String.class)))
            .thenThrow(new RuntimeException("Invalid token"));

        mockMvc.perform(get("/api/protected")
                .header("Authorization", "Bearer invalid-token")
                .contentType(MediaType.APPLICATION_JSON))
                .andExpect(status().isUnauthorized());
    }
} 