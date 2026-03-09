FROM alextselegidis/easyappointments:1.4.3

# Disable conflicting MPM modules, keep only mpm_prefork
RUN a2dismod mpm_worker mpm_event 2>/dev/null || true && \
    a2enmod mpm_prefork
