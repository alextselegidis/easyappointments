# Build from the official Easy!Appointments Docker image
FROM alextselegidis/easyappointments:latest

# Add an entrypoint to auto-create config.php from env vars
COPY deploy/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
