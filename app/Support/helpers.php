<?php

if (! function_exists('rupiah')) {
    /**
     * Format a numeric amount as Indonesian Rupiah, e.g. 150000 -> "Rp 150.000".
     *
     * Uses a '.' thousands separator and no decimal places, matching the spec.
     */
    function rupiah(int|float|string|null $amount): string
    {
        $value = (float) ($amount ?? 0);

        return 'Rp '.number_format($value, 0, ',', '.');
    }
}
