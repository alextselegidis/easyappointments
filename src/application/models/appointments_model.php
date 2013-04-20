<?php
class Appointments_Model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }
    
    public function add($appointmentData) {
        try {
            if (!$this->exists($appointmentData)) {
                $this->insert($appointmentData);
            } else {
                $this->update($appointmentData);
            }
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }
    
    public function exists($appointmentData) {
        return false; // @task This check is going to be more complicated than just checking an email (customers model)
    }
    
    private function insert($appointmentData) {
        $this->db->insert('ea_appointments', $appointmentData);
    }
    
    private function update($appointmentData) {
        $this->db->where('id', $appointmentData['id']);
        $this->db->update('ea_appointments', $appointmentData);
    }
    
    public function delete($appointmentId) {
        
    }
}
?>
