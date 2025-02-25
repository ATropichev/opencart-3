<?php
class ControllerExtensionPaymentCheque extends Controller {
    private array $error = [];

    public function index(): void {
        $this->load->language('extension/payment/cheque');

        $this->document->setTitle($this->language->get('heading_title'));

        // Settings
        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('payment_cheque', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['payable'])) {
            $data['error_payable'] = $this->error['payable'];
        } else {
            $data['error_payable'] = '';
        }

        $data['breadcrumbs'] = [];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/payment/cheque', 'user_token=' . $this->session->data['user_token'], true)
        ];

        $data['action'] = $this->url->link('extension/payment/cheque', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

        if (isset($this->request->post['payment_cheque_payable'])) {
            $data['payment_cheque_payable'] = $this->request->post['payment_cheque_payable'];
        } else {
            $data['payment_cheque_payable'] = $this->config->get('payment_cheque_payable');
        }

        if (isset($this->request->post['payment_cheque_total'])) {
            $data['payment_cheque_total'] = $this->request->post['payment_cheque_total'];
        } else {
            $data['payment_cheque_total'] = $this->config->get('payment_cheque_total');
        }

        if (isset($this->request->post['payment_cheque_order_status_id'])) {
            $data['payment_cheque_order_status_id'] = (int)$this->request->post['payment_cheque_order_status_id'];
        } else {
            $data['payment_cheque_order_status_id'] = $this->config->get('payment_cheque_order_status_id');
        }

        // Order Statuses
        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if (isset($this->request->post['payment_cheque_geo_zone_id'])) {
            $data['payment_cheque_geo_zone_id'] = (int)$this->request->post['payment_cheque_geo_zone_id'];
        } else {
            $data['payment_cheque_geo_zone_id'] = $this->config->get('payment_cheque_geo_zone_id');
        }

        // Geo Zones
        $this->load->model('localisation/geo_zone');

        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        if (isset($this->request->post['payment_cheque_status'])) {
            $data['payment_cheque_status'] = $this->request->post['payment_cheque_status'];
        } else {
            $data['payment_cheque_status'] = $this->config->get('payment_cheque_status');
        }

        if (isset($this->request->post['payment_cheque_sort_order'])) {
            $data['payment_cheque_sort_order'] = $this->request->post['payment_cheque_sort_order'];
        } else {
            $data['payment_cheque_sort_order'] = $this->config->get('payment_cheque_sort_order');
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/cheque', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/payment/cheque')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['payment_cheque_payable']) {
            $this->error['payable'] = $this->language->get('error_payable');
        }

        return !$this->error;
    }
}