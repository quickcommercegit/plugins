<?php

function PAFE_has_license($license) {
    return isset($license) && isset($license['license_key']);
}

function PAFE_has_valid_license($license) {
    if (!isset($license) || !isset($license['license_key']) || !isset($license['status']) || $license['status'] !== 'A') {
        return false;
    }

    if (isset($license['lifetime']) && $license['lifetime']) {
        return true;
    } else if ($license['expired_at'] > time()) {
        return true;
    }
    return false;
}
