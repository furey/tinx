<?php

namespace Ajthinking\Tinx\Includes;

use Illuminate\Support\Str;

class IncludeManager
{
    /**
     * @param array $names
     * @return void
     * */
    public function generateIncludesFile($names)
    {
        $config = config('tinx');

        $contents = view('tinx::includes', compact('names', 'config'))->render();

        $contents = Str::replaceFirst('<php', '<?php', $contents);

        app('tinx.storage')->put('includes.php', $contents);
    }
}
