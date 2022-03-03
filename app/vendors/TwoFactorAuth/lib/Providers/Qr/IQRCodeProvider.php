<?php

// namespace Providers\Qr;

interface IQRCodeProvider
{
    public function getQRCodeImage($qrtext, $size);
    public function getMimeType();
}