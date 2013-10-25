<?php
/**
 * Find the database record id of an admin user.
 * 
 * @param array $admin Contains the admin data. The 'email'
 * value is required in order to find the record id.
 * @return int Returns the record id
 * @throws Exception When the 'email' value is not present 
 * on the $admin array.
 */
public function find_record_id($admin) {
  if (!isset($admin['email'])) {
    throw new Exception('Admin email was not provided: ' 
	    . print_r($admin, TRUE));
  }
	
  $result = $this->db
      ->select('ea_users.id')
      ->from('ea_users')
      ->join('ea_roles', 'ea_roles.id = ea_users.id_roles', 'inner')
      ->where('ea_users.email', $admin['email'])
      ->where('ea_roles.slug', DB_SLUG_ADMIN)
      ->get();
	
  if ($result->num_rows() == 0) {
    throw new Exception('Could not find admin record id.');
  }
	
  return intval($result->row()->id);
}