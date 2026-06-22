<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventInvitation;
use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use Illuminate\Support\Facades\Storage;

class InvitationImageRenderer
{
    public function render(EventInvitation $invitation): string
    {
        $invitation->loadMissing('event');

        [$canvas, $width, $height] = $this->makeCanvas($invitation->event);
        $qrImage = $this->makeQrImage($invitation->code, $invitation->event, $width, $height);
        [$x, $y] = $this->qrCoordinates($invitation->event, $width, $height, imagesx($qrImage), imagesy($qrImage));

        imagealphablending($canvas, true);
        imagecopy($canvas, $qrImage, $x, $y, 0, 0, imagesx($qrImage), imagesy($qrImage));

        ob_start();
        imagepng($canvas, null, 9);
        $png = (string) ob_get_clean();

        imagedestroy($canvas);
        imagedestroy($qrImage);

        return $png;
    }

    private function makeCanvas(Event $event): array
    {
        if ($event->invitation_background_path && Storage::disk('public')->exists($event->invitation_background_path)) {
            $source = @imagecreatefromstring(Storage::disk('public')->get($event->invitation_background_path));

            if ($source !== false) {
                $width = imagesx($source);
                $height = imagesy($source);
                $canvas = imagecreatetruecolor($width, $height);
                imagealphablending($canvas, false);
                imagesavealpha($canvas, true);
                imagecopy($canvas, $source, 0, 0, 0, 0, $width, $height);
                imagedestroy($source);

                return [$canvas, $width, $height];
            }
        }

        $width = 1080;
        $height = 1350;
        $canvas = app(DefaultInvitationBackgroundRenderer::class)->makeCanvas($width, $height);

        return [$canvas, $width, $height];
    }

    private function makeQrImage(string $value, Event $event, int $canvasWidth, int $canvasHeight)
    {
        $requestedSize = $event->invitationQrSize();
        $maxSize = max(80, min($requestedSize, $canvasWidth, $canvasHeight));
        $quietZone = 2;
        $padding = 0;

        $qrCode = Encoder::encode($value, ErrorCorrectionLevel::L());
        $matrix = $qrCode->getMatrix();
        $moduleCount = $matrix->getWidth();
        $contentSize = max(1, $maxSize - ($padding * 2));
        $moduleSize = max(1, (int) floor($contentSize / ($moduleCount + ($quietZone * 2))));
        $qrPixelSize = $moduleSize * ($moduleCount + ($quietZone * 2));
        $imageSize = $maxSize;

        $image = imagecreatetruecolor($imageSize, $imageSize);
        imagealphablending($image, false);
        imagesavealpha($image, true);

        $transparent = imagecolorallocatealpha($image, 255, 255, 255, 127);
        $dark = imagecolorallocate($image, 15, 23, 42);
        imagefilledrectangle($image, 0, 0, $imageSize, $imageSize, $transparent);

        $contentOffset = (int) floor(($imageSize - $qrPixelSize) / 2);
        $offset = $contentOffset + ($quietZone * $moduleSize);

        for ($y = 0; $y < $moduleCount; $y++) {
            for ($x = 0; $x < $moduleCount; $x++) {
                if ($matrix->get($x, $y) !== 1) {
                    continue;
                }

                $left = $offset + ($x * $moduleSize);
                $top = $offset + ($y * $moduleSize);
                imagefilledrectangle(
                    $image,
                    $left,
                    $top,
                    $left + $moduleSize - 1,
                    $top + $moduleSize - 1,
                    $dark
                );
            }
        }

        return $image;
    }

    private function qrCoordinates(Event $event, int $canvasWidth, int $canvasHeight, int $qrWidth, int $qrHeight): array
    {
        $margin = 32;
        $horizontal = [
            'left' => max(0, min($margin, $canvasWidth - $qrWidth)),
            'center' => max(0, (int) floor(($canvasWidth - $qrWidth) / 2)),
            'right' => max(0, $canvasWidth - $qrWidth - $margin),
        ];
        $vertical = [
            'top' => max(0, min($margin, $canvasHeight - $qrHeight)),
            'middle' => max(0, (int) floor(($canvasHeight - $qrHeight) / 2)),
            'bottom' => max(0, $canvasHeight - $qrHeight - $margin),
        ];

        [$xKey, $yKey] = explode('-', $event->invitationQrPosition());

        return [$horizontal[$xKey] ?? $horizontal['right'], $vertical[$yKey] ?? $vertical['bottom']];
    }
}
