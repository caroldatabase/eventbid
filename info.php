<?php

echo "command";

exec('git add --all');
exec('git commit -m "new change"');

exec('git push origin dev');
