FROM alextselegidis/easyappointments:1.4.3

# Remove conflicting MPM module configs
RUN rm -f /etc/apache2/mods-enabled/mpm_*.load && \
    rm -f /etc/apache2/mods-enabled/mpm_*.conf && \
    rm -f /etc/apache2/mods-available/mpm_worker.* && \
    rm -f /etc/apache2/mods-available/mpm_event.*

# Enable only mpm_prefork
RUN a2enmod mpm_prefork
