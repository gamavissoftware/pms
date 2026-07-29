<?php
/**
 * stubs.php
 *
 * Minimal stand-ins for the CodeIgniter pieces Bom_engine touches, so
 * the real application/libraries/Bom_engine.php can be loaded and
 * exercised with no framework and no database.
 *
 * The stub models implement the same read methods as the real
 * Bom_master_model / Bom_item_model and return the same shapes, backed
 * by rows parsed straight out of abom_seed.sql.
 *
 * PHP 7.4 compatible.
 */

/** Bom_engine calls $this->CI->load->model(...) in its constructor. */
class Abom_stub_loader
{
    public function model($name, $alias = '')
    {
        // Models are pre-attached to the stub instance; nothing to do.
    }
}

class Abom_stub_master_model
{
    private $families = array();
    private $sections = array();
    private $formulas = array();
    private $features = array();
    private $rules    = array();

    public function __construct(Abom_seed_parser $seed)
    {
        foreach ($seed->rows('abom_plc_family') as $row) {
            $this->families[(int) $row->id] = $row;
        }
        $this->sections = $seed->rows('abom_section');
        $this->formulas = $seed->rows('abom_formula');
        $this->features = $seed->rows('abom_feature');

        // Priority ASC, id ASC — the real model's ORDER BY.
        $rules = array();
        foreach ($seed->rows('abom_plc_rule') as $row) {
            if ((int) $row->is_active === 1) {
                $rules[] = $row;
            }
        }
        usort($rules, function ($a, $b) {
            if ((int) $a->priority === (int) $b->priority) {
                return (int) $a->id - (int) $b->id;
            }
            return (int) $a->priority - (int) $b->priority;
        });
        $this->rules = $rules;
    }

    public function get_active_rules()
    {
        return $this->rules;
    }

    public function get_families()
    {
        return $this->families;
    }

    public function get_family($family_id)
    {
        $family_id = (int) $family_id;
        return isset($this->families[$family_id]) ? $this->families[$family_id] : null;
    }

    public function family_code($family_id)
    {
        $family = $this->get_family($family_id);
        return $family ? (string) $family->code : '';
    }

    public function family_defaults($family_id)
    {
        $family = $this->get_family($family_id);
        if (!$family) {
            return array('j4_units' => 0, 'battery_qty' => 0, 'panel_location' => '');
        }

        return array(
            'j4_units'       => (int) $family->default_j4_units,
            'battery_qty'    => (int) $family->default_battery,
            'panel_location' => (string) $family->default_panel_location,
        );
    }

    public function get_sections($family_id)
    {
        $out = array();
        foreach ($this->sections as $section) {
            if ((int) $section->plc_family_id === (int) $family_id) {
                $out[] = $section;
            }
        }
        return $out;
    }

    public function get_features()
    {
        $out = array();
        foreach ($this->features as $feature) {
            $out[$feature->code] = $feature;
        }
        return $out;
    }

    public function default_features()
    {
        $out = array();
        foreach ($this->features as $feature) {
            $out[$feature->code] = (int) $feature->default_on;
        }
        return $out;
    }

    public function count_sections()
    {
        return count($this->sections);
    }

    public function count_formulas()
    {
        return count($this->formulas);
    }

    public function count_features()
    {
        return count($this->features);
    }

    public function count_rules()
    {
        return count($this->rules);
    }
}

class Abom_stub_item_model
{
    /** @var array */
    private $items = array();

    public function __construct(Abom_seed_parser $seed)
    {
        // Section lookup, to reproduce the real model's LEFT JOIN.
        $sections = array();
        foreach ($seed->rows('abom_section') as $section) {
            $sections[(int) $section->id] = $section;
        }

        foreach ($seed->rows('abom_item') as $item) {
            $section_id = (int) $item->section_id;

            $item->section_name = isset($sections[$section_id]) ? $sections[$section_id]->name : null;
            $item->section_code = isset($sections[$section_id]) ? $sections[$section_id]->code : null;
            $item->section_sort = isset($sections[$section_id]) ? (int) $sections[$section_id]->sort_order : 0;

            $this->items[] = $item;
        }
    }

    /**
     * Active items for one family, ordered by section sort_order then
     * item id — matching Bom_item_model::get_by_family().
     */
    public function get_by_family($family_id)
    {
        $out = array();
        foreach ($this->items as $item) {
            if ((int) $item->plc_family_id === (int) $family_id && (int) $item->is_active === 1) {
                $out[] = $item;
            }
        }

        usort($out, function ($a, $b) {
            if ((int) $a->section_sort === (int) $b->section_sort) {
                return (int) $a->id - (int) $b->id;
            }
            return (int) $a->section_sort - (int) $b->section_sort;
        });

        return $out;
    }

    public function get_item($item_id)
    {
        foreach ($this->items as $item) {
            if ((int) $item->id === (int) $item_id) {
                return $item;
            }
        }
        return null;
    }

    public function all()
    {
        return $this->items;
    }

    public function count_items()
    {
        return count($this->items);
    }
}

/**
 * Stands in for CI_Config. Reads the REAL application/config/abom.php so
 * the helper's UOM map and quantity-lock rules are the shipped ones, not
 * a copy that can drift.
 */
class Abom_stub_config
{
    private $values = array();

    public function __construct($root)
    {
        $config = array();
        $file   = $root . '/application/config/abom.php';

        if (is_readable($file)) {
            defined('BASEPATH') OR define('BASEPATH', true);
            include $file;
        }

        $this->values = $config;
    }

    public function item($key, $section = '')
    {
        return isset($this->values[$key]) ? $this->values[$key] : null;
    }

    public function set_item($key, $value)
    {
        $this->values[$key] = $value;
    }
}

/** Stands in for the CI super-object returned by get_instance(). */
class Abom_stub_ci
{
    public $load;
    public $config;
    public $Bom_master_model;
    public $Bom_item_model;

    public function __construct(Abom_seed_parser $seed, $root = null)
    {
        if ($root === null) {
            $root = dirname(dirname(__DIR__));
        }

        $this->load             = new Abom_stub_loader();
        $this->config           = new Abom_stub_config($root);
        $this->Bom_master_model = new Abom_stub_master_model($seed);
        $this->Bom_item_model   = new Abom_stub_item_model($seed);
    }
}

/** Bom_engine's constructor does $this->CI =& get_instance(). */
if (!function_exists('get_instance')) {
    function &get_instance()
    {
        return $GLOBALS['ABOM_STUB_CI'];
    }
}
