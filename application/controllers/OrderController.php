<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OrderController extends CI_Controller {

    private function normalize_brand_report_date($date_value)
    {
        $date_value = trim((string) $date_value);
        if ($date_value === '') {
            return '';
        }

        $date = DateTime::createFromFormat('Y-m-d', $date_value);
        if ($date instanceof DateTime && $date->format('Y-m-d') === $date_value) {
            return $date_value;
        }

        return '';
    }

    private function get_brand_report_month_options()
    {
        return array(
            '04' => 'April',
            '05' => 'May',
            '06' => 'June',
            '07' => 'July',
            '08' => 'August',
            '09' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December',
            '01' => 'January',
            '02' => 'February',
            '03' => 'March',
        );
    }

    private function get_brand_report_filters()
    {
        $this->load->model('User_model', 'analyticsUser');
        $financial_year_options = $this->analyticsUser->get_available_financial_years();
        $financial_year_values = array();
        $month_options = $this->get_brand_report_month_options();
        $selected_financial_year = trim((string) $this->input->get('financial_year', true));
        $selected_month = trim((string) $this->input->get('month', true));
        $start_date_input = $this->normalize_brand_report_date($this->input->get('start_date', true));
        $end_date_input = $this->normalize_brand_report_date($this->input->get('end_date', true));

        foreach ($financial_year_options as $financial_year_option) {
            $financial_year_values[] = $financial_year_option['value'];
        }

        if ($selected_financial_year === '' || !in_array($selected_financial_year, $financial_year_values, true)) {
            $selected_financial_year = !empty($financial_year_options) ? $financial_year_options[0]['value'] : $this->analyticsUser->get_financial_year_details('')['value'];
        }

        if (!array_key_exists($selected_month, $month_options)) {
            $selected_month = '';
        }

        if ($start_date_input !== '' && $end_date_input !== '' && strtotime($start_date_input) > strtotime($end_date_input)) {
            $temp = $start_date_input;
            $start_date_input = $end_date_input;
            $end_date_input = $temp;
        }

        $financial_year_details = $this->analyticsUser->get_financial_year_details($selected_financial_year);
        $custom_range_active = ($start_date_input !== '' && $end_date_input !== '');
        $effective_start_date = $financial_year_details['start_date'];
        $effective_end_date = $financial_year_details['end_date'];
        $filter_mode_label = $financial_year_details['label'];

        if ($selected_month !== '') {
            $target_year = ((int) $selected_month >= 4) ? (int) $financial_year_details['start_year'] : (int) $financial_year_details['end_year'];
            $month_start_date = $target_year . '-' . $selected_month . '-01';
            $effective_start_date = $month_start_date;
            $effective_end_date = date('Y-m-t', strtotime($month_start_date));
            $filter_mode_label = $month_options[$selected_month] . ' ' . $target_year;
        }

        if ($custom_range_active) {
            $effective_start_date = $start_date_input;
            $effective_end_date = $end_date_input;
            $filter_mode_label = date('d M Y', strtotime($effective_start_date)) . ' to ' . date('d M Y', strtotime($effective_end_date));
        }

        return array(
            'financial_year' => $financial_year_details['value'],
            'financial_year_label' => $financial_year_details['label'],
            'financial_year_options' => $financial_year_options,
            'month' => $selected_month,
            'month_label' => $selected_month !== '' ? $month_options[$selected_month] : 'All Months',
            'month_options' => $month_options,
            'start_date' => $start_date_input,
            'end_date' => $end_date_input,
            'custom_range_active' => $custom_range_active,
            'effective_start_date' => $effective_start_date,
            'effective_end_date' => $effective_end_date,
            'filter_mode_label' => $filter_mode_label,
        );
    }

    private function build_brand_report_summary($rows)
    {
        $summary = array(
            'total_orders' => 0,
            'total_order_value' => 0,
            'avg_order_value' => 0,
            'active_brands' => 0,
            'top_brand_name' => 'No data found',
            'top_brand_order_value' => 0,
            'top_brand_order_count' => 0,
            'latest_order_date' => '',
        );

        if (empty($rows)) {
            return $summary;
        }

        $summary['active_brands'] = count($rows);

        foreach ($rows as $index => $row) {
            $summary['total_orders'] += (int) $row['order_count'];
            $summary['total_order_value'] += (float) $row['total_order_value'];

            if (!empty($row['latest_order_date']) && ($summary['latest_order_date'] === '' || strtotime($row['latest_order_date']) > strtotime($summary['latest_order_date']))) {
                $summary['latest_order_date'] = $row['latest_order_date'];
            }

            if ($index === 0) {
                $summary['top_brand_name'] = $row['name'];
                $summary['top_brand_order_value'] = (float) $row['total_order_value'];
                $summary['top_brand_order_count'] = (int) $row['order_count'];
            }
        }

        if ($summary['total_orders'] > 0) {
            $summary['avg_order_value'] = $summary['total_order_value'] / $summary['total_orders'];
        }

        return $summary;
    }

    public function index() {
        $filters = $this->get_brand_report_filters();
        $order_data_by_brand = $this->get_order_data($filters);
        $data['order_data_by_brand'] = $order_data_by_brand;
        $data['brand_report_filters'] = $filters;
        $data['brand_report_summary'] = $this->build_brand_report_summary($order_data_by_brand);
        $this->load->view('dashboard/filterorderbybrand',$data);
    }

    private function get_order_data($filters = array()) {
        $this->load->model('OrderModel');
        if (!empty($filters)) {
            return $this->OrderModel->get_brand_performance_rows($filters);
        }
        return $this->OrderModel->get_orders();
    }

    public function get_order_details($brand_tag) {
        $this->load->model('OrderModel');
        $filters = $this->get_brand_report_filters();
        $data = $this->OrderModel->get_brand_order_details($brand_tag, $filters);

        foreach ($data as &$order) {
            if (!empty($order['podate'])) {
                $order['podate'] = date('d-m-Y', strtotime($order['podate']));
            }
        }
        echo json_encode($data);
    }

    public function get_filtered_orders() {
        // Fetch filtered order data based on date range
        $year = $this->input->get('year');
        $month = $this->input->get('month');
        
        $this->load->model('OrderModel');
        $data = $this->OrderModel->get_orders_by_date_range($year, $month);

        // Format the PO Date
        foreach ($data as &$order) {
            $order['podate'] = date('d-m-Y', strtotime($order['podate']));
        }

        echo json_encode($data);
    }

    public function get_filtered_orders_betweendate() {
        // Fetch filtered order data based on date range
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        
        $this->load->model('OrderModel');
        $data = $this->OrderModel->get_orders_by_date_range_between($start_date, $end_date);

        // Format the PO Date
        foreach ($data as &$order) {
            $order['podate'] = date('d-m-Y', strtotime($order['podate']));
        }

        echo json_encode($data);
    }


     public function marketingteamreport() {
        
        $this->load->view('dashboard/marketing-work-report');
    }

    

// Dashboard.php (Controller)
public function get_total_sales() {
    $this->load->model('Marketing_report_model'); // Load the model
    $total_sales = $this->Marketing_report_model->fetch_total_sales(); // Fetch total sales

    // Format total sales in Indian money format
    $formatted_sales = $this->format_indian_currency($total_sales);

    echo json_encode(['total_sales' => $formatted_sales]); // Return the formatted sales as JSON
}

private function format_indian_currency($amount) {
    if (!is_numeric($amount)) return '₹0.00';
    $amount = number_format($amount, 2, '.', ''); // Ensure two decimal places
    $amount_parts = explode('.', $amount);
    $whole_number = $amount_parts[0];
    $decimal = isset($amount_parts[1]) ? $amount_parts[1] : '00';

    $last_three = substr($whole_number, -3);
    $remaining_numbers = substr($whole_number, 0, -3);

    $formatted_whole = ($remaining_numbers != '') ? $remaining_numbers . ',' . $last_three : $last_three;
    $formatted_whole = preg_replace('/(\d)(?=(\d\d)+\d$)/', "$1,", $remaining_numbers) . ',' . $last_three;

    return '₹' . $formatted_whole . '.' . $decimal;
}



// Dashboard.php (Controller)
public function get_inquiry_success_rate() {
    $this->load->model('Marketing_report_model'); // Load the model

    // Fetch total quotations and total orders won
    $total_quotations = $this->Marketing_report_model->fetch_total_quotations();
    $total_orders_won = $this->Marketing_report_model->fetch_total_orders_won();

    // Calculate success rate
    $success_rate = ($total_quotations > 0) ? round(($total_orders_won / $total_quotations) * 100, 2) : 0;

    echo json_encode(['success_rate' => $success_rate]); // Return the success rate as JSON
}

public function get_new_clients() {
    $this->load->model('Marketing_report_model'); // Load the model
    $new_clients = $this->Marketing_report_model->fetch_new_clients(); // Fetch data from the model

    if ($new_clients) {
        echo json_encode(['new_clients' => $new_clients]);
    } else {
        echo json_encode(['new_clients' => 0]); // Default value if no data
    }
}

public function get_order_won() {
    $this->load->model('Marketing_report_model'); // Load the model
    $order_won = $this->Marketing_report_model->fetch_order_won(); // Fetch data from the model

    if ($order_won) {
        echo json_encode(['order_won' => $order_won]);
    } else {
        echo json_encode(['order_won' => 0]); // Default value if no data
    }
}

public function get_quotation_follow_up() {
    $this->load->model('Marketing_report_model'); // Load the model
    $quotation_follow_up = $this->Marketing_report_model->fetch_quotation_follow_up(); // Fetch data from the model

    if ($quotation_follow_up) {
        echo json_encode(['quotation_follow_up' => $quotation_follow_up]);
    } else {
        echo json_encode(['quotation_follow_up' => 0]); // Default value if no data
    }
}


public function get_order_breakdown() {
    $this->load->model('Marketing_report_model'); // Load the model
    $order_breakdown = $this->Marketing_report_model->fetch_order_breakdown(); // Fetch data from the model

    if ($order_breakdown) {
        echo json_encode($order_breakdown); // Return data as JSON
    } else {
        echo json_encode(['domestic' => 0, 'international' => 0]); // Default values if no data
    }
}

private function get_order_report_date_expression($alias = 'a')
{
    return "COALESCE(NULLIF(DATE(" . $alias . ".podate), '0000-00-00'), NULLIF(DATE(" . $alias . ".added_on), '0000-00-00'))";
}

private function get_report_month_options()
{
    return array(
        '04' => 'April',
        '05' => 'May',
        '06' => 'June',
        '07' => 'July',
        '08' => 'August',
        '09' => 'September',
        '10' => 'October',
        '11' => 'November',
        '12' => 'December',
        '01' => 'January',
        '02' => 'February',
        '03' => 'March',
    );
}

private function get_report_users()
{
    return $this->db->select('user_id, CONCAT_WS(" ", title, first_name, last_name) as full_name')
        ->from('system_users')
        ->where('department_id', 9)
        ->where('user_status', 1)
        ->order_by('first_name')
        ->get()
        ->result();
}

private function get_order_report_filters($override_user_id = null)
{
    $this->load->model('User_model', 'analyticsUser');
    $financial_year_options = $this->analyticsUser->get_available_financial_years();
    $selected_financial_year = trim((string) $this->input->get('financial_year', true));
    $available_financial_year_values = array();
    $month_options = $this->get_report_month_options();

    foreach ($financial_year_options as $financial_year_option) {
        $available_financial_year_values[] = $financial_year_option['value'];
    }

    if ($selected_financial_year === '' || !in_array($selected_financial_year, $available_financial_year_values, true)) {
        $selected_financial_year = !empty($financial_year_options) ? $financial_year_options[0]['value'] : $this->analyticsUser->get_financial_year_details('')['value'];
    }

    $financial_year_details = $this->analyticsUser->get_financial_year_details($selected_financial_year);
    $selected_month = trim((string) $this->input->get('month', true));
    if (!array_key_exists($selected_month, $month_options)) {
        $selected_month = '';
    }

    if ($override_user_id !== null) {
        $selected_user_id = trim((string) $override_user_id);
    } else {
        $selected_user_id = trim((string) $this->input->get('user_id', true));
    }

    if (!ctype_digit($selected_user_id)) {
        $selected_user_id = '';
    }

    return array(
        'financial_year' => $financial_year_details['value'],
        'financial_year_label' => $financial_year_details['label'],
        'financial_year_start_date' => $financial_year_details['start_date'],
        'financial_year_end_date' => $financial_year_details['end_date'],
        'month' => $selected_month,
        'month_label' => $selected_month !== '' ? $month_options[$selected_month] : 'All Months',
        'user_id' => $selected_user_id,
        'financial_year_options' => $financial_year_options,
        'month_options' => $month_options,
    );
}

private function get_sales_data_rows($filters)
{
    $date_expression = $this->get_order_report_date_expression('a');

    $this->db->select(
        'b.user_id, ' .
        'COALESCE(NULLIF(CONCAT_WS(" ", b.title, b.first_name, b.last_name), ""), "Unassigned") as name, ' .
        'COUNT(a.id) as order_count, ' .
        'SUM(COALESCE(a.order_value, 0)) as total_order_value, ' .
        'AVG(COALESCE(a.order_value, 0)) as avg_order_value, ' .
        'MAX(' . $date_expression . ') as latest_order_date',
        false
    );
    $this->db->from('poreceived a');
    $this->db->join('system_users b', 'b.user_id = a.added_by', 'left');
    $this->db->where($date_expression . ' >= ' . $this->db->escape($filters['financial_year_start_date']), null, false);
    $this->db->where($date_expression . ' <= ' . $this->db->escape($filters['financial_year_end_date']), null, false);

    if (!empty($filters['month'])) {
        $this->db->where('MONTH(' . $date_expression . ') = ' . (int) $filters['month'], null, false);
    }

    if (!empty($filters['user_id'])) {
        $this->db->where('a.added_by', (int) $filters['user_id']);
    }

    $this->db->group_by('a.added_by');
    $this->db->order_by('total_order_value', 'DESC');
    $this->db->order_by('name', 'ASC');

    return $this->db->get()->result();
}

private function build_sales_data_summary($rows)
{
    $summary = array(
        'total_orders' => 0,
        'total_order_value' => 0,
        'avg_order_value' => 0,
        'active_agents' => 0,
        'top_agent_name' => 'No data found',
        'top_agent_order_value' => 0,
        'top_agent_order_count' => 0,
        'latest_order_date' => '',
    );

    if (empty($rows)) {
        return $summary;
    }

    $summary['active_agents'] = count($rows);

    foreach ($rows as $index => $row) {
        $summary['total_orders'] += (int) $row->order_count;
        $summary['total_order_value'] += (float) $row->total_order_value;

        if (!empty($row->latest_order_date) && ($summary['latest_order_date'] === '' || strtotime($row->latest_order_date) > strtotime($summary['latest_order_date']))) {
            $summary['latest_order_date'] = $row->latest_order_date;
        }

        if ($index === 0) {
            $summary['top_agent_name'] = $row->name;
            $summary['top_agent_order_value'] = (float) $row->total_order_value;
            $summary['top_agent_order_count'] = (int) $row->order_count;
        }
    }

    if ($summary['total_orders'] > 0) {
        $summary['avg_order_value'] = $summary['total_order_value'] / $summary['total_orders'];
    }

    return $summary;
}

private function get_df_cost_order_rows($filters)
{
    $date_expression = $this->get_order_report_date_expression('a');
    $df_label_expression = 'COALESCE(NULLIF(a.df_number, ""), NULLIF(df.df_no, ""), "DF Not Mapped")';
    $marketing_expression = 'COALESCE(NULLIF(CONCAT_WS(" ", u.title, u.first_name, u.last_name), ""), "Unassigned")';

    $this->db->select(
        'COALESCE(a.df_id, 0) as df_id, ' .
        $df_label_expression . ' as df_no, ' .
        'a.added_by as marketing_user_id, ' .
        $marketing_expression . ' as marketing_person, ' .
        'COUNT(a.id) as po_count, ' .
        'GROUP_CONCAT(DISTINCT NULLIF(a.pono, "") ORDER BY a.podate SEPARATOR ", ") as po_numbers, ' .
        'GROUP_CONCAT(DISTINCT NULLIF(a.company_name, "") ORDER BY a.company_name SEPARATOR ", ") as customer_names, ' .
        'SUM(COALESCE(a.order_value, 0)) as total_order_value, ' .
        'AVG(COALESCE(a.order_value, 0)) as avg_order_value, ' .
        'MIN(' . $date_expression . ') as first_order_date, ' .
        'MAX(' . $date_expression . ') as latest_order_date',
        false
    );
    $this->db->from('poreceived a');
    $this->db->join('df_release df', 'df.id = a.df_id', 'left');
    $this->db->join('system_users u', 'u.user_id = a.added_by', 'left');
    $this->db->where($date_expression . ' >= ' . $this->db->escape($filters['financial_year_start_date']), null, false);
    $this->db->where($date_expression . ' <= ' . $this->db->escape($filters['financial_year_end_date']), null, false);

    if (!empty($filters['month'])) {
        $this->db->where('MONTH(' . $date_expression . ') = ' . (int) $filters['month'], null, false);
    }

    if (!empty($filters['user_id'])) {
        $this->db->where('a.added_by', (int) $filters['user_id']);
    }

    $this->db->group_by('a.df_id');
    $this->db->group_by('a.df_number');
    $this->db->group_by('df.df_no');
    $this->db->group_by('a.added_by');
    $this->db->group_by('u.title');
    $this->db->group_by('u.first_name');
    $this->db->group_by('u.last_name');
    $this->db->order_by('total_order_value', 'DESC');
    $this->db->order_by('latest_order_date', 'DESC');

    return $this->db->get()->result();
}

private function build_df_cost_order_summary($rows)
{
    $summary = array(
        'total_dfs' => 0,
        'total_orders' => 0,
        'total_order_value' => 0,
        'avg_df_value' => 0,
        'active_marketing_people' => 0,
        'top_df_no' => 'No data found',
        'top_marketing_person' => 'No data found',
        'top_df_order_value' => 0,
        'top_df_order_count' => 0,
        'latest_order_date' => '',
    );

    if (empty($rows)) {
        return $summary;
    }

    $seen_dfs = array();
    $seen_marketing_people = array();

    foreach ($rows as $index => $row) {
        $df_key = ((int) $row->df_id > 0) ? 'id-' . (int) $row->df_id : 'label-' . (string) $row->df_no;
        $marketing_key = ((int) $row->marketing_user_id > 0) ? 'user-' . (int) $row->marketing_user_id : 'name-' . (string) $row->marketing_person;

        $seen_dfs[$df_key] = true;
        $seen_marketing_people[$marketing_key] = true;
        $summary['total_orders'] += (int) $row->po_count;
        $summary['total_order_value'] += (float) $row->total_order_value;

        if (!empty($row->latest_order_date) && ($summary['latest_order_date'] === '' || strtotime($row->latest_order_date) > strtotime($summary['latest_order_date']))) {
            $summary['latest_order_date'] = $row->latest_order_date;
        }

        if ($index === 0) {
            $summary['top_df_no'] = $row->df_no;
            $summary['top_marketing_person'] = $row->marketing_person;
            $summary['top_df_order_value'] = (float) $row->total_order_value;
            $summary['top_df_order_count'] = (int) $row->po_count;
        }
    }

    $summary['total_dfs'] = count($seen_dfs);
    $summary['active_marketing_people'] = count($seen_marketing_people);

    if ($summary['total_dfs'] > 0) {
        $summary['avg_df_value'] = $summary['total_order_value'] / $summary['total_dfs'];
    }

    return $summary;
}

private function build_df_cost_marketing_summary($rows)
{
    $marketing_summary = array();

    foreach ($rows as $row) {
        $marketing_key = !empty($row->marketing_person) ? $row->marketing_person : 'Unassigned';

        if (!isset($marketing_summary[$marketing_key])) {
            $marketing_summary[$marketing_key] = array(
                'name' => $marketing_key,
                'po_count' => 0,
                'df_keys' => array(),
                'df_count' => 0,
                'total_order_value' => 0,
            );
        }

        $df_key = ((int) $row->df_id > 0) ? 'id-' . (int) $row->df_id : 'label-' . (string) $row->df_no;
        $marketing_summary[$marketing_key]['po_count'] += (int) $row->po_count;
        $marketing_summary[$marketing_key]['df_keys'][$df_key] = true;
        $marketing_summary[$marketing_key]['total_order_value'] += (float) $row->total_order_value;
    }

    foreach ($marketing_summary as $marketing_key => $marketing_row) {
        $marketing_summary[$marketing_key]['df_count'] = count($marketing_row['df_keys']);
    }

    usort($marketing_summary, function ($left, $right) {
        if ($left['total_order_value'] == $right['total_order_value']) {
            return strcmp($left['name'], $right['name']);
        }

        return ($left['total_order_value'] < $right['total_order_value']) ? 1 : -1;
    });

    return $marketing_summary;
}

public function userwisemonthlyreport()
{
    $filters = $this->get_order_report_filters();
    $report_users = $this->get_report_users();
    $sales_rows = $this->get_sales_data_rows($filters);
    $sales_summary = $this->build_sales_data_summary($sales_rows);
    $selected_user_label = 'All Sales Agents';

    if (!empty($filters['user_id'])) {
        foreach ($report_users as $report_user) {
            if ((string) $report_user->user_id === (string) $filters['user_id']) {
                $selected_user_label = $report_user->full_name;
                break;
            }
        }
    }

    $data = array(
        'performance_rows' => $sales_rows,
        'performance_summary' => $sales_summary,
        'report_filters' => $filters,
        'report_users' => $report_users,
        'selected_user_label' => $selected_user_label,
    );

    $this->load->view('dashboard/filterbyuserperformancemonthwise', $data);
}

public function dfwisecostorderreport()
{
    $filters = $this->get_order_report_filters();
    $report_users = $this->get_report_users();
    $df_rows = $this->get_df_cost_order_rows($filters);
    $df_summary = $this->build_df_cost_order_summary($df_rows);
    $selected_user_label = 'All Marketing Persons';

    if (!empty($filters['user_id'])) {
        foreach ($report_users as $report_user) {
            if ((string) $report_user->user_id === (string) $filters['user_id']) {
                $selected_user_label = $report_user->full_name;
                break;
            }
        }
    }

    $data = array(
        'df_rows' => $df_rows,
        'df_summary' => $df_summary,
        'report_filters' => $filters,
        'report_users' => $report_users,
        'selected_user_label' => $selected_user_label,
    );

    $this->load->view('dashboard/df_wise_cost_order_report', $data);
}

public function exportdfwisecostorderexcel()
{
    if (!class_exists('ZipArchive')) {
        show_error('Excel export needs the PHP zip extension (ZipArchive), which is not enabled on this server.', 500);
    }

    $filters = $this->get_order_report_filters();
    $report_users = $this->get_report_users();
    $df_rows = $this->get_df_cost_order_rows($filters);
    $df_summary = $this->build_df_cost_order_summary($df_rows);
    $marketing_summary = $this->build_df_cost_marketing_summary($df_rows);
    $selected_user_label = 'All Marketing Persons';

    if (!empty($filters['user_id'])) {
        foreach ($report_users as $report_user) {
            if ((string) $report_user->user_id === (string) $filters['user_id']) {
                $selected_user_label = $report_user->full_name;
                break;
            }
        }
    }

    $this->load->library('excel');
    $object = new PHPExcel();
    $object->getProperties()
        ->setCreator('PMS')
        ->setTitle('DF Wise Cost Order Value Report')
        ->setSubject('DF Wise Cost Order Value Report');

    $last_detail_col = 'L';
    $currency_format = '"Rs " #,##0';
    $summary_title_style = array(
        'font' => array('bold' => true, 'color' => array('rgb' => 'FFFFFF'), 'size' => 16),
        'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '17324D')),
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER),
    );
    $section_style = array(
        'font' => array('bold' => true, 'color' => array('rgb' => 'FFFFFF')),
        'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '248A84')),
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
    );
    $table_header_style = array(
        'font' => array('bold' => true, 'color' => array('rgb' => 'FFFFFF')),
        'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '175CD3')),
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrap' => true),
        'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'D9E2EC'))),
    );
    $body_style = array(
        'alignment' => array('vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP, 'wrap' => true),
        'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'D9E2EC'))),
    );
    $total_style = array(
        'font' => array('bold' => true, 'color' => array('rgb' => 'FFFFFF')),
        'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '17324D')),
        'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'D9E2EC'))),
    );
    $kpi_label_style = array(
        'font' => array('bold' => true, 'color' => array('rgb' => '475467')),
        'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'EAF4FF')),
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
        'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'D9E2EC'))),
    );
    $kpi_value_style = array(
        'font' => array('bold' => true, 'color' => array('rgb' => '17324D'), 'size' => 12),
        'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
        'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => 'D9E2EC'))),
    );

    $summary_sheet = $object->setActiveSheetIndex(0);
    $summary_sheet->setTitle('Executive Summary');
    $summary_sheet->mergeCells('A1:H1');
    $summary_sheet->setCellValue('A1', 'DF Wise Cost Order Value Report');
    $summary_sheet->getStyle('A1:H1')->applyFromArray($summary_title_style);
    $summary_sheet->getRowDimension(1)->setRowHeight(28);
    $summary_sheet->mergeCells('A2:H2');
    $summary_sheet->setCellValue('A2', 'Financial Year: ' . $filters['financial_year_label'] . ' | Month: ' . $filters['month_label'] . ' | Marketing Person: ' . $selected_user_label . ' | Generated: ' . date('d M Y h:i A'));

    $kpis = array(
        array('label' => 'Total Cost Order Value', 'value' => (float) $df_summary['total_order_value'], 'format' => $currency_format),
        array('label' => 'DFs Tracked', 'value' => (int) $df_summary['total_dfs'], 'format' => '#,##0'),
        array('label' => 'PO Count', 'value' => (int) $df_summary['total_orders'], 'format' => '#,##0'),
        array('label' => 'Marketing Persons', 'value' => (int) $df_summary['active_marketing_people'], 'format' => '#,##0'),
        array('label' => 'Average DF Value', 'value' => (float) $df_summary['avg_df_value'], 'format' => $currency_format),
        array('label' => 'Top DF Value', 'value' => (float) $df_summary['top_df_order_value'], 'format' => $currency_format),
    );
    $kpi_positions = array('A4:B5', 'C4:D5', 'E4:F5', 'G4:H5', 'A7:B8', 'C7:D8');
    foreach ($kpis as $index => $kpi) {
        $range_parts = explode(':', $kpi_positions[$index]);
        $start_cell = $range_parts[0];
        $end_cell = $range_parts[1];
        $label_cell = $start_cell;
        $value_cell = preg_replace('/\d+/', '', $start_cell) . ((int) preg_replace('/\D+/', '', $start_cell) + 1);
        $summary_sheet->mergeCells($label_cell . ':' . preg_replace('/\d+/', '', $end_cell) . preg_replace('/\D+/', '', $start_cell));
        $summary_sheet->mergeCells($value_cell . ':' . preg_replace('/\d+/', '', $end_cell) . preg_replace('/\D+/', '', $value_cell));
        $summary_sheet->setCellValue($label_cell, $kpi['label']);
        $summary_sheet->setCellValue($value_cell, $kpi['value']);
        $summary_sheet->getStyle($label_cell . ':' . preg_replace('/\d+/', '', $end_cell) . preg_replace('/\D+/', '', $start_cell))->applyFromArray($kpi_label_style);
        $summary_sheet->getStyle($value_cell . ':' . preg_replace('/\d+/', '', $end_cell) . preg_replace('/\D+/', '', $value_cell))->applyFromArray($kpi_value_style);
        $summary_sheet->getStyle($value_cell)->getNumberFormat()->setFormatCode($kpi['format']);
    }
    $summary_sheet->mergeCells('E7:H8');
    $summary_sheet->setCellValue('E7', 'Top DF: ' . $df_summary['top_df_no'] . "\nMarketing Person: " . $df_summary['top_marketing_person']);
    $summary_sheet->getStyle('E7:H8')->applyFromArray($kpi_value_style);
    $summary_sheet->getStyle('E7:H8')->getAlignment()->setWrapText(true)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

    $summary_sheet->mergeCells('A10:D10');
    $summary_sheet->setCellValue('A10', 'Marketing Person Summary');
    $summary_sheet->getStyle('A10:D10')->applyFromArray($section_style);
    $marketing_header_row = 11;
    foreach (array('Rank', 'Marketing Person', 'DF Count', 'Total Value') as $col_index => $label) {
        $summary_sheet->setCellValueByColumnAndRow($col_index, $marketing_header_row, $label);
    }
    $summary_sheet->getStyle('A11:D11')->applyFromArray($table_header_style);
    $row = 12;
    foreach (array_slice($marketing_summary, 0, 10) as $index => $marketing_row) {
        $summary_sheet->setCellValue('A' . $row, $index + 1);
        $summary_sheet->setCellValue('B' . $row, $marketing_row['name']);
        $summary_sheet->setCellValue('C' . $row, (int) $marketing_row['df_count']);
        $summary_sheet->setCellValue('D' . $row, (float) $marketing_row['total_order_value']);
        $row++;
    }
    $marketing_last_row = max(12, $row - 1);
    $summary_sheet->getStyle('A12:D' . $marketing_last_row)->applyFromArray($body_style);
    $summary_sheet->getStyle('D12:D' . $marketing_last_row)->getNumberFormat()->setFormatCode($currency_format);

    $summary_sheet->mergeCells('F10:J10');
    $summary_sheet->setCellValue('F10', 'Top DF Values');
    $summary_sheet->getStyle('F10:J10')->applyFromArray($section_style);
    foreach (array('Rank', 'DF No', 'Marketing Person', 'PO Count', 'Total Value') as $col_index => $label) {
        $summary_sheet->setCellValueByColumnAndRow($col_index + 5, 11, $label);
    }
    $summary_sheet->getStyle('F11:J11')->applyFromArray($table_header_style);
    $row = 12;
    foreach (array_slice($df_rows, 0, 10) as $index => $df_row) {
        $summary_sheet->setCellValue('F' . $row, $index + 1);
        $summary_sheet->setCellValueExplicit('G' . $row, (string) $df_row->df_no, PHPExcel_Cell_DataType::TYPE_STRING);
        $summary_sheet->setCellValue('H' . $row, $df_row->marketing_person);
        $summary_sheet->setCellValue('I' . $row, (int) $df_row->po_count);
        $summary_sheet->setCellValue('J' . $row, (float) $df_row->total_order_value);
        $row++;
    }
    $top_df_last_row = max(12, $row - 1);
    $summary_sheet->getStyle('F12:J' . $top_df_last_row)->applyFromArray($body_style);
    $summary_sheet->getStyle('J12:J' . $top_df_last_row)->getNumberFormat()->setFormatCode($currency_format);

    if (!empty($df_rows)) {
        $this->add_order_report_excel_bar_chart(
            $summary_sheet,
            'Top DF Cost Order Value',
            "'Executive Summary'!\$G\$12:\$G\$" . $top_df_last_row,
            array(
                array(
                    'label' => "'Executive Summary'!\$J\$11",
                    'values' => "'Executive Summary'!\$J\$12:\$J\$" . $top_df_last_row,
                ),
            ),
            'A25',
            'F42',
            true
        );
    }

    if (!empty($marketing_summary)) {
        $this->add_order_report_excel_pie_chart(
            $summary_sheet,
            'Marketing Value Share',
            "'Executive Summary'!\$D\$11",
            "'Executive Summary'!\$B\$12:\$B\$" . $marketing_last_row,
            "'Executive Summary'!\$D\$12:\$D\$" . $marketing_last_row,
            'G25',
            'J42'
        );
    }

    foreach (array('A' => 10, 'B' => 24, 'C' => 12, 'D' => 17, 'E' => 4, 'F' => 9, 'G' => 17, 'H' => 24, 'I' => 12, 'J' => 17) as $column => $width) {
        $summary_sheet->getColumnDimension($column)->setWidth($width);
    }
    $summary_sheet->freezePane('A11');

    $details_sheet = $object->createSheet();
    $details_sheet->setTitle('DF Details');
    $details_sheet->mergeCells('A1:' . $last_detail_col . '1');
    $details_sheet->setCellValue('A1', 'DF Wise Cost Order Value Detail');
    $details_sheet->getStyle('A1:' . $last_detail_col . '1')->applyFromArray($summary_title_style);
    $details_sheet->mergeCells('A2:' . $last_detail_col . '2');
    $details_sheet->setCellValue('A2', 'Financial Year: ' . $filters['financial_year_label'] . ' | Month: ' . $filters['month_label'] . ' | Marketing Person: ' . $selected_user_label);

    $headers = array('Rank', 'DF No', 'Marketing Person', 'Financial Year', 'Customers', 'PO Count', 'PO Numbers', 'First PO Date', 'Latest PO Date', 'Total Cost Order Value', 'Average PO Value', 'Value Share');
    $header_row = 4;
    foreach ($headers as $col_index => $label) {
        $details_sheet->setCellValueByColumnAndRow($col_index, $header_row, $label);
    }
    $details_sheet->getStyle('A' . $header_row . ':' . $last_detail_col . $header_row)->applyFromArray($table_header_style);

    $row = $header_row + 1;
    $grand_total = (float) $df_summary['total_order_value'];
    foreach ($df_rows as $index => $df_row) {
        $details_sheet->setCellValue('A' . $row, $index + 1);
        $details_sheet->setCellValueExplicit('B' . $row, (string) $df_row->df_no, PHPExcel_Cell_DataType::TYPE_STRING);
        $details_sheet->setCellValue('C' . $row, $df_row->marketing_person);
        $details_sheet->setCellValue('D' . $row, $filters['financial_year_label']);
        $details_sheet->setCellValue('E' . $row, !empty($df_row->customer_names) ? $df_row->customer_names : 'NA');
        $details_sheet->setCellValue('F' . $row, (int) $df_row->po_count);
        $details_sheet->setCellValueExplicit('G' . $row, !empty($df_row->po_numbers) ? (string) $df_row->po_numbers : 'NA', PHPExcel_Cell_DataType::TYPE_STRING);
        $details_sheet->setCellValue('H' . $row, !empty($df_row->first_order_date) ? date('d-m-Y', strtotime($df_row->first_order_date)) : 'NA');
        $details_sheet->setCellValue('I' . $row, !empty($df_row->latest_order_date) ? date('d-m-Y', strtotime($df_row->latest_order_date)) : 'NA');
        $details_sheet->setCellValue('J' . $row, (float) $df_row->total_order_value);
        $details_sheet->setCellValue('K' . $row, (float) $df_row->avg_order_value);
        $details_sheet->setCellValue('L' . $row, $grand_total > 0 ? ((float) $df_row->total_order_value / $grand_total) : 0);
        $row++;
    }
    $last_data_row = max($header_row + 1, $row - 1);
    $total_row = $last_data_row + 1;
    $details_sheet->setCellValue('A' . $total_row, 'Grand Total');
    $details_sheet->mergeCells('A' . $total_row . ':E' . $total_row);
    $details_sheet->setCellValue('F' . $total_row, (int) $df_summary['total_orders']);
    $details_sheet->setCellValue('J' . $total_row, (float) $df_summary['total_order_value']);
    $details_sheet->setCellValue('K' . $total_row, !empty($df_summary['total_orders']) ? ((float) $df_summary['total_order_value'] / (int) $df_summary['total_orders']) : 0);
    $details_sheet->setCellValue('L' . $total_row, 1);

    $details_sheet->getStyle('A' . ($header_row + 1) . ':' . $last_detail_col . $last_data_row)->applyFromArray($body_style);
    $details_sheet->getStyle('A' . $total_row . ':' . $last_detail_col . $total_row)->applyFromArray($total_style);
    $details_sheet->getStyle('J' . ($header_row + 1) . ':K' . $total_row)->getNumberFormat()->setFormatCode($currency_format);
    $details_sheet->getStyle('L' . ($header_row + 1) . ':L' . $total_row)->getNumberFormat()->setFormatCode('0.0%');
    $details_sheet->getStyle('A' . ($header_row + 1) . ':D' . $total_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $details_sheet->getStyle('F' . ($header_row + 1) . ':F' . $total_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $details_sheet->freezePane('A5');
    $details_sheet->setAutoFilter('A' . $header_row . ':' . $last_detail_col . $last_data_row);

    foreach (array('A' => 8, 'B' => 16, 'C' => 24, 'D' => 15, 'E' => 34, 'F' => 11, 'G' => 28, 'H' => 14, 'I' => 14, 'J' => 20, 'K' => 18, 'L' => 12) as $column => $width) {
        $details_sheet->getColumnDimension($column)->setWidth($width);
    }

    $object->setActiveSheetIndex(0);
    $file_name = 'DF_Wise_Cost_Order_Value_' . preg_replace('/[^A-Za-z0-9]+/', '_', $filters['financial_year_label'] . '_' . $selected_user_label) . '.xlsx';
    $writer = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
    $writer->setIncludeCharts(true);

    if (ob_get_length()) {
        ob_end_clean();
    }

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $file_name . '"');
    header('Cache-Control: max-age=0');

    set_error_handler(function ($no, $str, $file = '', $line = 0) {
        return (strpos(str_replace('\\', '/', (string) $file), '/third_party/PHPExcel/') !== false);
    });

    try {
        $writer->save('php://output');
    } catch (Throwable $e) {
        restore_error_handler();
        throw $e;
    }

    restore_error_handler();
    exit;
}

private function add_order_report_excel_bar_chart($sheet, $title_text, $category_range, $series_definitions, $top_left, $bottom_right, $horizontal = false)
{
    $point_count = $this->count_order_report_excel_range_points($category_range);
    $data_series_labels = array();
    $data_series_values = array();

    foreach ($series_definitions as $series_definition) {
        $data_series_labels[] = new PHPExcel_Chart_DataSeriesValues('String', $series_definition['label'], null, 1);
        $data_series_values[] = new PHPExcel_Chart_DataSeriesValues('Number', $series_definition['values'], null, $point_count);
    }

    $x_axis_tick_values = array(
        new PHPExcel_Chart_DataSeriesValues('String', $category_range, null, $point_count)
    );

    $series = new PHPExcel_Chart_DataSeries(
        PHPExcel_Chart_DataSeries::TYPE_BARCHART,
        PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED,
        range(0, count($data_series_values) - 1),
        $data_series_labels,
        $x_axis_tick_values,
        $data_series_values
    );
    $series->setPlotDirection(
        $horizontal
            ? PHPExcel_Chart_DataSeries::DIRECTION_BAR
            : PHPExcel_Chart_DataSeries::DIRECTION_COL
    );

    $plot_area = new PHPExcel_Chart_PlotArea(null, array($series));
    $legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, null, false);
    $title = new PHPExcel_Chart_Title($title_text);
    $chart = new PHPExcel_Chart('df_value_' . md5($title_text . $top_left), $title, $legend, $plot_area, true, 0, null, null);
    $chart->setPlotVisibleOnly(false);
    $chart->setTopLeftPosition($top_left);
    $chart->setBottomRightPosition($bottom_right);
    $sheet->addChart($chart);
}

private function add_order_report_excel_pie_chart($sheet, $title_text, $series_label_range, $category_range, $value_range, $top_left, $bottom_right)
{
    $point_count = $this->count_order_report_excel_range_points($category_range);
    $label = array(
        new PHPExcel_Chart_DataSeriesValues('String', $series_label_range, null, 1)
    );
    $categories = array(
        new PHPExcel_Chart_DataSeriesValues('String', $category_range, null, $point_count)
    );
    $values = array(
        new PHPExcel_Chart_DataSeriesValues('Number', $value_range, null, $point_count)
    );

    $series = new PHPExcel_Chart_DataSeries(
        PHPExcel_Chart_DataSeries::TYPE_PIECHART,
        null,
        range(0, count($values) - 1),
        $label,
        $categories,
        $values
    );

    $plot_area = new PHPExcel_Chart_PlotArea(null, array($series));
    $legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, null, false);
    $title = new PHPExcel_Chart_Title($title_text);
    $chart = new PHPExcel_Chart('marketing_share_' . md5($title_text . $top_left), $title, $legend, $plot_area, true, 0, null, null);
    $chart->setPlotVisibleOnly(false);
    $chart->setTopLeftPosition($top_left);
    $chart->setBottomRightPosition($bottom_right);
    $sheet->addChart($chart);
}

private function count_order_report_excel_range_points($range)
{
    if (!preg_match('/\$?[A-Z]+\$?(\d+):\$?[A-Z]+\$?(\d+)/', (string) $range, $matches)) {
        return 1;
    }

    $start_row = (int) $matches[1];
    $end_row = (int) $matches[2];

    return max(1, $end_row - $start_row + 1);
}

public function get_sales_data($financial_year = null, $month = null, $user_id = null)
{
    $filters = $this->get_order_report_filters($user_id);

    if ($financial_year !== null && trim((string) $financial_year) !== '') {
        $this->load->model('User_model', 'analyticsUser');
        $financial_year_details = $this->analyticsUser->get_financial_year_details(trim((string) $financial_year));
        $filters['financial_year'] = $financial_year_details['value'];
        $filters['financial_year_label'] = $financial_year_details['label'];
        $filters['financial_year_start_date'] = $financial_year_details['start_date'];
        $filters['financial_year_end_date'] = $financial_year_details['end_date'];
    }

    if ($month !== null && trim((string) $month) !== '') {
        $month_options = $this->get_report_month_options();
        $requested_month = trim((string) $month);
        $filters['month'] = array_key_exists($requested_month, $month_options) ? $requested_month : '';
    }

    return $this->get_sales_data_rows($filters);
}

public function get_user_performance()
{
    echo json_encode($this->get_sales_data_rows($this->get_order_report_filters()));
}

public function get_users_for_dropdown()
{
    echo json_encode($this->get_report_users());
}

public function get_order_detailssss($user_id = null)
{
    $filters = $this->get_order_report_filters($user_id);
    $date_expression = $this->get_order_report_date_expression('poreceived');

    $this->db->select(
        'poreceived.company_name, poreceived.pono, ' .
        $date_expression . ' as podate, ' .
        'poreceived.order_value, system_users.title, system_users.first_name, system_users.last_name',
        false
    );
    $this->db->from('poreceived');
    $this->db->join('system_users', 'system_users.user_id = poreceived.added_by', 'left');
    $this->db->where($date_expression . ' >= ' . $this->db->escape($filters['financial_year_start_date']), null, false);
    $this->db->where($date_expression . ' <= ' . $this->db->escape($filters['financial_year_end_date']), null, false);

    if (!empty($filters['user_id'])) {
        $this->db->where('poreceived.added_by', (int) $filters['user_id']);
    }

    if (!empty($filters['month'])) {
        $this->db->where('MONTH(' . $date_expression . ') = ' . (int) $filters['month'], null, false);
    }

    $this->db->order_by($date_expression, 'DESC', false);
    echo json_encode($this->db->get()->result());
}




}
