FROM tutum/apache-php

MAINTAINER Kun Ran <rankun203@gmail.com>

# Note: this only exposes the port to other docker containers. You
# still have to bind to 80@host at runtime, as per the ACME spec.
EXPOSE 80
