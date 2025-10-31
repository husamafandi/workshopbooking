<?php
// Hard-mute docs endpoint to guarantee no output in any context.
// This avoids breaking JSON responses in AJAX operations.
http_response_code(204);
exit;
