<?php
namespace Bottel\Database\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $file
 * @property string $mime_type
 * @property string $filename
 * @property int $file_modified_at
 * @property bool $keep_alive_modifies
 * @property bool $one_time
 */
class BottelSymbolic extends Model {
    protected $table = "bottel_symbolic";

    protected $fillable = [
        'key',
        'file',
        'mime_type',
        'filename',
        'file_modified_at',
        'keep_alive_modifies',
        'one_time',
    ];
}
?>