<?php

namespace Ajthinking\Tinx\Includes;

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

        $contents = str_replace_first('<php', '<?php', $contents);

        app('tinx.storage')->put('includes.php', $contents);
    }
}
