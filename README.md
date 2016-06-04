gcloud-manager - A Google Cloud manager application
===================================================

Requirements
------------

1. PHP >= 5.4
2. Google Cloud Platform access
3. Service account key credentials downloaded from the Google Cloud Platform - "On Your Own Server" https://googlecloudplatform.github.io/gcloud-ruby/#/docs/v0.10.0/guides/authentication

Installation
------------

1. Clone the repository
2. Run `composer install`
3. Configure the following environment variables
```
export GOOGLE_APPLICATION_CREDENTIALS=~/.google-cloud-credentials/your-project-credentials.json
export GOOGLE_CLOUD_ZONE=europe-west1-d
```

Usage
-----

1. List the available commands:
```
app/console list
```
2. Compute engine commands:
```
  compute:instances:list   Lists the compute instances.
  compute:instances:start  Starts the compute instances.
  compute:instances:stop   Stops the compute instances.
```
