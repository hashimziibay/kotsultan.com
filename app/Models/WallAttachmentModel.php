<?php

namespace App\Models;

use CodeIgniter\Model;

class WallAttachmentModel extends Model
{
    protected $table            = 'wall_attachments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'wall_id',
        'file_path',
        'original_name',
        'mime_type',
        'file_type',
        'file_size',
        'display_order',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getForWall(int $wallId): array
    {
        $rows = $this->where('wall_id', $wallId)
            ->orderBy('display_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        return array_map([$this, 'withPublicUrl'], $rows);
    }

    public function withPublicUrl(array $row): array
    {
        $path = trim((string) ($row['file_path'] ?? ''));
        $row['url'] = $path !== ''
            ? (function_exists('base_url') ? base_url($path) : '/' . ltrim($path, '/'))
            : '';
        $row['is_image'] = ($row['file_type'] ?? '') === 'image';
        $row['is_pdf']   = ($row['file_type'] ?? '') === 'pdf';
        $row['is_word']  = ($row['file_type'] ?? '') === 'word';
        return $row;
    }

    public static function classifyExtension(string $ext): string
    {
        $ext = strtolower($ext);
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'], true)) {
            return 'image';
        }
        if ($ext === 'pdf') {
            return 'pdf';
        }
        if (in_array($ext, ['doc', 'docx'], true)) {
            return 'word';
        }
        return 'other';
    }

    /**
     * @return list<string>
     */
    public static function allowedExtensions(): array
    {
        return ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'pdf', 'doc', 'docx'];
    }
}
