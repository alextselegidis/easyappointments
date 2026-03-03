<?php defined('BASEPATH') or exit('No direct script access allowed');

class Payments_api_v1 extends EA_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('api');
        $this->load->library('tenant_context');

        $this->api->auth();
    }

    public function stripe_intents(): void
    {
        try {
            if (!$this->db->table_exists('stripe_payment_intents')) {
                json_response([
                    'data' => [],
                    'meta' => [
                        'count' => 0,
                        'page' => 1,
                        'length' => 20,
                    ],
                ]);

                return;
            }

            $tenant_id = $this->tenant_context->resolve_tenant_id();
            $status = trim((string) request('status'));
            $from = trim((string) request('from'));
            $to = trim((string) request('to'));
            $appointment_id = (int) request('appointmentId', 0);
            $page = max(1, (int) request('page', 1));
            $length = max(1, min(100, (int) request('length', 20)));
            $offset = ($page - 1) * $length;

            $base = $this->db->from('stripe_payment_intents')->where('tenant_id', $tenant_id);

            if ($status !== '') {
                $base->where('status', $status);
            }

            if ($appointment_id > 0) {
                $base->where('appointment_id', $appointment_id);
            }

            if ($from !== '') {
                $base->where('created_at >=', $from . ' 00:00:00');
            }

            if ($to !== '') {
                $base->where('created_at <=', $to . ' 23:59:59');
            }

            $total_query = clone $base;
            $total = (int) $total_query->count_all_results();

            $records = $base
                ->select('*')
                ->order_by('id', 'DESC')
                ->limit($length, $offset)
                ->get()
                ->result_array();

            json_response([
                'data' => $records,
                'meta' => [
                    'count' => $total,
                    'page' => $page,
                    'length' => $length,
                ],
            ]);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }

    public function stripe_intent(int $id): void
    {
        try {
            if (!$this->db->table_exists('stripe_payment_intents')) {
                response('', 404);

                return;
            }

            $tenant_id = $this->tenant_context->resolve_tenant_id();

            $record = $this->db
                ->get_where('stripe_payment_intents', ['id' => $id, 'tenant_id' => $tenant_id])
                ->row_array();

            if (empty($record)) {
                response('', 404);

                return;
            }

            json_response($record);
        } catch (Throwable $e) {
            json_exception($e);
        }
    }
}
