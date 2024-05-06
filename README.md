# madeit fixed

Brief description of your project.

## Prerequisites

- PHP 8.2
- Composer installed
- Docker
- Docker Compose


### Endpoints

- **GET /providers**
  - Description: Retrieve a list of providers.
  - Controller: `ProviderController`
  - Method: `providers`

- **GET /files**
  - Description: Retrieve a list of files.
  - Controller: `FileController`
  - Method: `files`

- **POST /upload/image**
  - Description: Upload an image file.
  - Controller: `FileController`
  - Method: `uploadImage`

- **POST /upload/video**
  - Description: Upload a video file.
  - Controller: `FileController`
  - Method: `uploadVideo`


## Getting Started

1. Clone the repository.
2. Run the `run.sh` script.
3. Access the application at [http://localhost:8080](http://localhost:8080).



## Installation
```bash
bash run.sh
