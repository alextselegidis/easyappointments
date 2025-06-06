name: Build and push Docker image to dockerhub

on:
  push:
    branches: [ "main" ]
    
jobs:

  build:

    runs-on: ubuntu-latest

    steps:
    - name: Check out the repo
      uses: actions/checkout@v4

    - name: Login to dockerhub
      uses: docker/login-action@v3
      with:
       username: ${{ secrets.DOCKERHUB_USERNAME }}
       password: ${{ secrets.DOCKERHUB_TOKEN }}

    - name: Build and push to dockerhub
      uses: docker/build-push-action@v5
      with:
       context:
       push: true
       tags: kicsipixel/githubaction:latest
