Laravel Distributed System with Docker and SQLite
This project demonstrates a distributed system architecture using Laravel, Docker, and SQLite. It consists of two separate microservices, each with its own SQLite database, and a proxy server that handles external requests.

 ![Infrastructure](infrastructure.pdf)
Overview
The system is composed of:

Authentication Microservice

Handles user management and authentication.
Uses an SQLite database to store user data.
Caches user sessions and tokens in a Redis memory store.
Posts Microservice

Manages user posts and related functionalities.
Uses an SQLite database to store post data.

Proxy Server

Acts as the single point of entry for external requests.
Routes requests to the appropriate microservice based on the endpoint.

Architecture

Each microservice communicates internally within the Docker environment, ensuring secure and efficient interaction.

The proxy server is the only component exposed to the external network, maintaining isolation for the microservices.
Redis is used for caching user session tokens and mapping bearer tokens to IP addresses.
Endpoints
Authentication Microservice: Handles /auth endpoints for user registration, login, and authentication.
Posts Microservice: Manages /posts endpoints for creating, reading, updating, and deleting posts.

Technologies


Laravel: PHP framework used for developing both microservices.

Docker: Containerization platform to manage and deploy the microservices.

SQLite: Lightweight database for persistent storage in each microservice.

Redis: In-memory data structure store for caching and session management.

Nginx: Proxy server to route external requests to the appropriate microservice.
