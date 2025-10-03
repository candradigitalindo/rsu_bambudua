<?php

namespace App\Http\Controllers\Concerns;

trait LogsActivity
{
    /**
     * Set explicit subject/module/payload for the current request so middleware can log it.
     *
     * Usage in controller/service:
     *   $this->activity('Merubah Tanggal Lahir Pasien', ['pasien_id' => $id]);
     */
    protected function activity(string $subject, array $payload = null, ?string $module = null): void
    {
        request()->attributes->set('activity_subject', $subject);
        if ($payload !== null) {
            request()->attributes->set('activity_payload', $payload);
        }
        if ($module !== null) {
            request()->attributes->set('activity_module', $module);
        }
    }
}
