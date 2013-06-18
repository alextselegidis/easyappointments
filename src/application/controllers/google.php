<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Google extends CI_Controller {
    /**
     * Authorize Google Calendar API usage for a specific provider.
     * 
     * Since it is required to follow the web application flow, in order to retrieve
     * a refresh token from the Google API service, this method is going to authorize 
     * the given provider.
     * 
     * @param int $provider_id The provider id, for whom the sync authorization is 
     * made.
     */
    public function oauth($provider_id) {
        $this->load->library('Google_Sync');
        
        // @task Create auth link and redirect browser window.
    }
    
    /**
     * Callback method for the Google Calendar API authorization process.
     * 
     * Once the user grants consent with his Google Calendar data usage, the Google
     * OAuth service will redirect him back in this page. Here we are going to store
     * the refresh token, because this is what will be used to generate access tokens
     * in the future.
     * 
     * <strong>IMPORTANT!</strong> Because it is necessary to authorize the application
     * using the web server flow (see official documentation of OAuth), every 
     * Easy!Appointments installation should use its own calendar api key. So in every
     * api console account, the "http://path-to-e!a/google/oauth_callback" should be 
     * included in the allowed redirect urls.
     */
    public function oauth_callback() {
        // @task Store refresh token.
    }
}

/* End of file google.php */
/* Location: ./application/controllers/google.php */