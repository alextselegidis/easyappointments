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
    	// Store the provider id for use on the callback function.
    	if (!isset($_SESSION)) {
            @session_start();
        }
    	$_SESSION['oauth_provider_id'] = $provider_id;
    	
        // Redirect browser to google user content page.
        $this->load->library('Google_Sync');
        header('Location: ' . $this->google_sync->get_auth_url());        
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
     * included in an allowed redirect url.
     */
    public function oauth_callback() {
       	if (isset($_GET['code'])) {
       		$this->load->library('Google_Sync');
       		$token = $this->google_sync->authenticate($_GET['code']);
       		
       		// Store the token into the database for future reference.
            if (!isset($_SESSION)) {
                @session_start();
            }
            
       		if (isset($_SESSION['oauth_provider_id'])) {
       			$this->load->model('providers_model');
       			
                $this->providers_model->set_setting('google_sync', TRUE, 
       					$_SESSION['oauth_provider_id']);
       			$this->providers_model->set_setting('google_token', $token, 
       					$_SESSION['oauth_provider_id']);
                
       		} else {
       			echo '<h1>Sync provider id not specified!</h1>';
       		}
            
       	} else {
       		echo '<h1>Authorization Failed!</h1>';
       	}
    }
}

/* End of file google.php */
/* Location: ./application/controllers/google.php */