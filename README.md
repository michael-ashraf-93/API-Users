# API-Users

This is A laravel API list, create, update and delete users

Users schema:
- id (primary key).
- email (String) , Input is required/valid email.
- password (string) Input is required, confirmed, and hashed has min length of 10 chars.
- first_name (string), Input is required.
- last_name (string), Input is required.
- photo (String), Input is required as base64 data.

Used technologies:
- Laravel
- MySQL

Authentication and Privacy:
- Authentication endpoints using oauth2 (Registration and Login using "JWT")
- Users show (All Users or Single User) endpoint is a public method (guest users can view it).
- Write methods (create, update and delete) are protected (user must be logged in).
- CORS are enabled.
- Rate usage limit with 20 requests/minute on all endpoints is applied.
