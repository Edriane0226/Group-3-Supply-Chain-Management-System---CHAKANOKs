<?php

namespace App\Models;

use CodeIgniter\Model;

class SystemSettingModel extends Model
{
    protected $table            = 'system_settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'setting_key',
        'setting_value',
        'setting_group',
        'setting_type',
        'description',
        'is_public',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get a setting value by key
     */
    public function getSetting(string $key, $default = null)
    {
        $setting = $this->where('setting_key', $key)->first();
        
        if (!$setting) {
            return $default;
        }

        return $this->castValue($setting['setting_value'], $setting['setting_type']);
    }

    /**
     * Set a setting value
     */
    public function setSetting(string $key, $value, string $group = 'general', string $type = 'text', string $description = null): bool
    {
        $existing = $this->where('setting_key', $key)->first();

        $data = [
            'setting_key'   => $key,
            'setting_value' => is_array($value) ? json_encode($value) : (string) $value,
            'setting_group' => $group,
            'setting_type'  => $type,
            'description'   => $description,
        ];

        if ($existing) {
            return $this->update($existing['id'], $data);
        }

        return (bool) $this->insert($data);
    }

    /**
     * Get all settings
     */
    public function getAllSettings(): array
    {
        $settings = $this->orderBy('setting_group', 'ASC')
                         ->orderBy('setting_key', 'ASC')
                         ->findAll();

        $result = [];
        foreach ($settings as $setting) {
            $result[$setting['setting_key']] = $this->castValue($setting['setting_value'], $setting['setting_type']);
        }

        return $result;
    }

    /**
     * Get settings by group
     */
    public function getByGroup(string $group): array
    {
        return $this->where('setting_group', $group)
                    ->orderBy('setting_key', 'ASC')
                    ->findAll();
    }

    /**
     * Get grouped settings
     */
    public function getGroupedSettings(): array
    {
        $settings = $this->orderBy('setting_group', 'ASC')
                         ->orderBy('setting_key', 'ASC')
                         ->findAll();

        $grouped = [];
        foreach ($settings as $setting) {
            $grouped[$setting['setting_group']][] = $setting;
        }

        return $grouped;
    }

    /**
     * Get public settings only
     */
    public function getPublicSettings(): array
    {
        $settings = $this->where('is_public', 1)->findAll();

        $result = [];
        foreach ($settings as $setting) {
            $result[$setting['setting_key']] = $this->castValue($setting['setting_value'], $setting['setting_type']);
        }

        return $result;
    }

    /**
     * Update multiple settings
     */
    public function updateSettings(array $settings): bool
    {
        $db = \Config\Database::connect();
        $db->transStart();

        foreach ($settings as $key => $value) {
            $existing = $this->where('setting_key', $key)->first();
            if ($existing) {
                $this->update($existing['id'], ['setting_value' => $value]);
            }
        }

        $db->transComplete();

        return $db->transStatus();
    }

    /**
     * Delete a setting
     */
    public function deleteSetting(string $key): bool
    {
        return (bool) $this->where('setting_key', $key)->delete();
    }

    /**
     * Cast value based on type
     */
    private function castValue($value, string $type)
    {
        switch ($type) {
            case 'number':
                return is_numeric($value) ? (strpos($value, '.') !== false ? (float) $value : (int) $value) : 0;
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'json':
                return json_decode($value, true) ?? [];
            default:
                return $value;
        }
    }

    /**
     * Get available setting groups
     */
    public function getGroups(): array
    {
        return $this->select('setting_group')
                    ->distinct()
                    ->orderBy('setting_group', 'ASC')
                    ->findAll();
    }
}

