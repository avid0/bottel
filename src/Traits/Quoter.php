<?php
namespace Bottel\Traits;

use Bottel\Quote;

trait Quoter {    
    /**
     * quotedFirstName
     *
     * @param  ?string $format
     * @return ?string
     */
    public function quotedFirstName(string $format = 'raw'): string {
        return Quote::format($this->first_name ?? '', $format) ?: null;
    }

    /**
     * quotedLastName
     *
     * @param  ?string $format
     * @return ?string
     */
    public function quotedLastName(string $format = 'raw'): string {
        return Quote::format($this->last_name ?? '', $format) ?: null;
    }

    /**
     * quotedFullName
     *
     * @param  ?string $format
     * @return ?string
     */
    public function quotedFullName(string $format = 'raw'): string {
        return Quote::format(($this->first_name ?? '') . (isset($this->last_name) ? ' ' . $this->last_name : ''), $format) ?: null;
    }

    /**
     * quotedUsername
     *
     * @param  ?string $format
     * @return ?string
     */
    public function quotedUsername(string $format = 'raw'): string {
        return Quote::format($this->username ?? '', $format) ?: null;
    }

    /**
     * quotedTitle
     *
     * @param  ?string $format
     * @return ?string
     */
    public function quotedTitle(string $format = 'raw'): string {
        return Quote::format($this->title ?? '', $format) ?: null;
    }

    /**
     * quotedFileName
     *
     * @param  ?string $format
     * @return ?string
     */
    public function quotedFileName(string $format = 'raw'): string {
        return Quote::format($this->file_name ?? '', $format) ?: null;
    }

    /**
     * quotedPerformer
     *
     * @param  ?string $format
     * @return ?string
     */
    public function quotedPerformer(string $format = 'raw'): string {
        return Quote::format($this->performer ?? '', $format) ?: null;
    }
}