<?php
function json_read($fitxer) {
    return json_decode(file_get_contents($fitxer), true);
}

function json_write($fitxer, $data) {
    file_put_contents($fitxer, json_encode($data, JSON_PRETTY_PRINT));
}
