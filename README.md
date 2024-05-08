# madeit fixed

This is the solution of Opti

## Prerequisites

- PHP 8.2
- Composer installed
- Docker
- Docker Compose


### Endpoints

- **GET /api/providers**
  - Description: Retrieve a list of providers.
  - Controller: `ProviderController`
  - Method: `providers`

- **GET /api/files**
  - Description: Retrieve a list of files with pagination.
  - Controller: `FileController`
  - Method: `files`

  **GET /api/files?mediaType=image&page=1** it can be image,video,audio with pagination.
  - Description: Retrieve a list of files with filtring mediaType 
  - Controller: `FileController`
  - Method: `files`

  **GET /api/files?uploadDate=YYYY-MM-DD&page=1** it can be image,video,audio with pagination.
  - Description: Retrieve a list of files with filtring mediaType 
  - Controller: `FileController`
  - Method: `files`

- **POST /api/upload/image**
  - Description: Upload an image file. validation included
  - Controller: `FileController`
  - Method: `uploadImage`

- **POST /api/upload/video**
  - Description: Upload a video file. validation included
  - Controller: `FileController`
  - Method: `uploadVideo`


## Getting Started

1. Clone the repository.
2. Run the `start.sh` script.
3. Access the application at [http://localhost:8080](http://localhost:8080).



## Installation
```bash
bash start.sh
```

## To run Unit Tests using PEST in LARAVEL Just Run the script 'run_tests.sh'
```bash
bash run_tests.sh
```
## Routes are inside src/routes/api.php

