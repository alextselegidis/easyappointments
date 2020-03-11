<?php

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.4.0
 * ---------------------------------------------------------------------------- */

namespace EA\Engine\Core;

/**
 * Class QueryBuilder
 *
 * @package EA\Engine\Core
 */
class QueryBuilder {
    /**
     * @var \EA\Engine\Core\Framework
     */
    private $framework;

    /**
     * QueryBuilder constructor.
     *
     * @param \EA\Engine\Core\Framework $framework
     */
    public function __construct(Framework $framework)
    {
        $this->framework = $framework;
    }


    /**
     * Count matching query rows.
     *
     * @param string $table
     * @param mixed $conditions
     *
     * @return int
     */
    public function count($table, $conditions)
    {
        return $this->framework->db->get_where($table, $conditions)->num_rows();
    }

    /**
     * Perform an arbitrary query to the databbase.
     *
     * @param $query
     *
     * @return mixed
     */
    public function query($query)
    {
        return $this->framework->db->query($query);
    }

    /**
     * Get a single database row, based on the provided conditions.
     *
     * @param string $table
     * @param mixed $conditions
     *
     * @return mixed
     */
    public function row($table, $conditions)
    {
        return $this->framework->db->get_where($table, $conditions)->row_array();
    }

    /**
     * Get a collection of rows, based on the provided parameters.
     *
     * @param string $table
     * @param mixed|null $conditions
     * @param int|null $limit
     * @param int|null $offset
     * @param mixed|null $order
     *
     * @return mixed
     */
    public function collection($table, $conditions = NULL, $limit = NULL, $offset = NULL, $order = NULL)
    {
        if ($limit)
        {
            $this->framework->db->limit($limit);
        }

        if ($limit && $offset)
        {
            $this->framework->db->limit($limit, $offset);
        }

        if ($order)
        {
            $this->framework->db->order_by($order);
        }

        if ($conditions)
        {
            $this->framework->db->where($conditions);
        }

        return $this->framework->db->get($table)->result_array();
    }

    /**
     * Insert a new database row.
     *
     * @param string $table
     * @param array $row
     *
     * @return int Returns the inserted ID.
     */
    public function insert($table, $row)
    {
        $this->framework->db->insert($table, $row);

        return $this->framework->db->insert_id();
    }

    /**
     * Update an existing database row.
     *
     * @param string $table
     * @param array $row
     * @param mixed $conditions
     *
     * @return mixed Returns the operation result.
     */
    public function update($table, $row, $conditions)
    {
        return $this->framework->db->update($table, $row, $conditions);
    }

    /**
     * Delete an existing database row.
     *
     * @param string $table
     * @param array $row
     * @param mixed $conditions
     *
     * @return mixed
     */
    public function delete($table, $row, $conditions)
    {
        return $this->framework->db->delete($table, $row, $conditions);
    }

    /**
     * Escape a raw value before using it in a query.
     *
     * @param string $value
     *
     * @return string
     */
    public function escape($value)
    {
        return $this->framework->db->escape($value);
    }
}
