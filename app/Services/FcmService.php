<?php
namespace App\Services;

use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Illuminate\Support\Facades\Log; // <-- Solusi untuk error Log

class FcmService
{
    protected $messaging;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    /**
     * Mengirim notifikasi ke satu device (Contoh: Tagihan Baru)
     */
    public function sendToUser($fcmToken, $title, $body, $data = [])
    {
        if (!$fcmToken) return false;

        // Solusi anti-error Intelephense: Menggunakan struktur Array
        $payload = [
            'token' => $fcmToken,
            'notification' => [
                'title' => $title,
                'body'  => $body,
            ],
        ];

        // Pastikan data tambahan disisipkan hanya jika tidak kosong
        if (!empty($data)) {
            $payload['data'] = $data;
        }

        $message = CloudMessage::fromArray($payload);

        try {
            $this->messaging->send($message);
            return true;
        } catch (\Exception $e) {
            Log::error('FCM Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mengirim notifikasi broadcast ke semua device (Contoh: Pengumuman)
     */
    public function sendToTopic($topic, $title, $body, $data = [])
    {
        $payload = [
            'topic' => $topic,
            'notification' => [
                'title' => $title,
                'body'  => $body,
            ],
        ];

        if (!empty($data)) {
            $payload['data'] = $data;
        }

        $message = CloudMessage::fromArray($payload);

        try {
            $this->messaging->send($message);
            return true;
        } catch (\Exception $e) {
            Log::error('FCM Topic Error: ' . $e->getMessage());
            return false;
        }
    }
}